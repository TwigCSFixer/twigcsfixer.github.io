# Examples of custom rules

## Short Example Title

Then, the easiest way to write a custom rule is to implement the `TwigCsFixer\Rules\AbstractRule` class
or the `TwigCsFixer\Rules\AbstractFixableRule` if the rule can be automatically fixed.

```php
final class MyCustomRule extends \TwigCsFixer\Rules\AbstractRule {
    protected function process(int $tokenIndex, \TwigCsFixer\Token\Tokens $tokens): void
    {
        $token = $tokens->get($tokenIndex);
        if (!$token->isMatching(...)) {
            // Skip if the token is not matching some conditions.
            return;
        }
    }
}
```

## Another Example Title

Rules can also be based on the Twig Node and NodeVisitor logic. Because they are
different from the default token based rules, these rules have some limitations:

- they cannot be fixable.
- they can only report the line with the error but not the token position.

### With headings

Still, these rules can be easier to be written for some static analysis.
You can get inspiration from the `src/Rules/Node` folder.

```php
final class MyCustomRule extends \TwigCsFixer\Rules\Node\AbstractNodeRule {
    public function enterNode(\Twig\Node\Node $node, \Twig\Environment $env): Node
    {
        // Do some logic
    }
}
```

### Another Heading

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor 
incididunt ut labore et dolore magnaaliqua. Ut enim ad minim veniam, quis 
nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.

## Yet Another Example Title
