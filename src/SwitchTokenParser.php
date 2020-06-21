<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\twigswitch;

use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Based on rejected Twig pull request: https://github.com/twigphp/Twig/pull/185
 */
class SwitchTokenParser extends AbstractTokenParser
{
    public function getTag(): string
    {
        return 'switch';
    }

    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $nodes = [
            'value' => $this->parser->getExpressionParser()->parseExpression(),
        ];

        $stream->expect(Token::BLOCK_END_TYPE);

        // There can be some whitespace between the {% switch %} and first {% case %} tag.
        while ($stream->getCurrent()->getType() === Token::TEXT_TYPE &&
            trim($stream->getCurrent()->getValue()) === ''
        ) {
            $stream->next();
        }

        $stream->expect(Token::BLOCK_START_TYPE);

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
                        if ($stream->test(Token::OPERATOR_TYPE, 'or')) {
                            $stream->next();
                        } else {
                            break;
                        }
                    }
                    $stream->expect(Token::BLOCK_END_TYPE);
                    $body = $this->parser->subparse([$this, 'decideIfFork']);
                    $cases[] = new Node([
                        'values' => new Node($values),
                        'body' => $body
                    ]);
                    break;
                case 'default':
                    $stream->expect(Token::BLOCK_END_TYPE);
                    $nodes['default'] = $this->parser->subparse([$this, 'decideIfEnd']);
                    break;
                case 'endswitch':
                    $end = true;
                    break;
                default:
                    throw new SyntaxError(
                        sprintf(
                            'Unexpected end of template. Twig was looking for the following tags "case", "default", or "endswitch" to close the "switch" block started at line %d)',
                            $lineno
                        ),
                        -1
                    );
            }
        }

        $nodes['cases'] = new Node($cases);

        $stream->expect(Token::BLOCK_END_TYPE);

        return new SwitchNode($nodes, [], $lineno, $this->getTag());
    }

    public function decideIfFork(Token $token): bool
    {
        return $token->test(['case', 'default', 'endswitch']);
    }

    public function decideIfEnd(Token $token): bool
    {
        return $token->test(['endswitch']);
    }
}
