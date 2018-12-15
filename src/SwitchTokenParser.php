<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\twigswitch;

use Twig_Node;
use Twig_Token;
use Twig_TokenParser;
use Twig_Error_Syntax;

/**
 * Based on rejected Twig pull request: https://github.com/twigphp/Twig/pull/185
 */
class SwitchTokenParser extends Twig_TokenParser
{
    public function getTag(): string
    {
        return 'switch';
    }

    public function parse(Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $nodes = [
            'value' => $this->parser->getExpressionParser()->parseExpression(),
        ];

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        // There can be some whitespace between the {% switch %} and first {% case %} tag.
        while ($stream->getCurrent()->getType() === Twig_Token::TEXT_TYPE &&
            trim($stream->getCurrent()->getValue()) === ''
        ) {
            $stream->next();
        }

        $stream->expect(Twig_Token::BLOCK_START_TYPE);

        $expressionParser = $this->parser->getExpressionParser();
        $cases = [];
        $end = false;

        while (! $end) {
            $next = $stream->next();

            switch ($next->getValue()) {
                case 'case':
                    $values = [];
                    while (true) {
                        $values[] = $expressionParser->parsePrimaryExpression();
                        // Multiple allowed values?
                        if ($stream->test(Twig_Token::OPERATOR_TYPE, 'or')) {
                            $stream->next();
                        } else {
                            break;
                        }
                    }
                    $stream->expect(Twig_Token::BLOCK_END_TYPE);
                    $body = $this->parser->subparse([$this, 'decideIfFork']);
                    $cases[] = new Twig_Node([
                        'values' => new Twig_Node($values),
                        'body' => $body
                    ]);
                    break;
                case 'default':
                    $stream->expect(Twig_Token::BLOCK_END_TYPE);
                    $nodes['default'] = $this->parser->subparse([$this, 'decideIfEnd']);
                    break;
                case 'endswitch':
                    $end = true;
                    break;
                default:
                    throw new Twig_Error_Syntax(
                        sprintf(
                            'Unexpected end of template. Twig was looking for the following tags "case", "default", or "endswitch" to close the "switch" block started at line %d)',
                            $lineno
                        ),
                        -1
                    );
            }
        }

        $nodes['cases'] = new Twig_Node($cases);

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new SwitchNode($nodes, [], $lineno, $this->getTag());
    }

    public function decideIfFork(Twig_Token $token): bool
    {
        return $token->test(['case', 'default', 'endswitch']);
    }

    public function decideIfEnd(Twig_Token $token): bool
    {
        return $token->test(['endswitch']);
    }
}
