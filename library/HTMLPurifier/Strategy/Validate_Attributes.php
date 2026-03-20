<?php

declare(strict_types=1);

/**
 * Validate all attributes in the tokens.
 */

class HTMLPurifier_Strategy_ValidateAttributes extends HTMLPurifier_Strategy
{
    /**
     * Validates and sanitises all attributes on start and empty tokens.
     *
     * For each start or empty tag, delegates to HTMLPurifier_AttrValidator which
     * checks every attribute against its registered HTMLPurifier_AttrDef definition.
     * Attributes that fail validation (wrong type, forbidden value, unsafe URI, etc.)
     * are silently removed from the token.
     *
     * @security This is the second XSS-prevention pass.  It catches attribute-based
     *           injection vectors that survive element filtering, including:
     *           - Event handlers (onclick, onerror, onload, …) not in the allowlist.
     *           - javascript: / vbscript: / data: URIs in href, src, action, etc.
     *           - CSS expressions injected via the style attribute.
     *           - Malformed or excessively long attribute values.
     *
     * @security Tokens armored with $token->armor['ValidateAttributes'] = true are
     *           skipped; this flag is set by RemoveForeignElements when required
     *           attributes have already been validated.
     *
     * @complexity O(n * a) where n is the number of tokens and a is the average
     *             number of attributes per token; in practice O(n) for typical HTML.
     *
     * @param HTMLPurifier_Token[] $tokens  The token stream (after RemoveForeignElements).
     * @param HTMLPurifier_Config  $config  The current purification configuration.
     * @param HTMLPurifier_Context $context Per-purification shared state.
     *
     * @return HTMLPurifier_Token[] The same token array with invalid attributes removed.
     */
    public function execute($tokens, $config, $context)
    {
        // setup validator
        $validator = new HTMLPurifier_AttrValidator();

        $token = false;
        $context->register('CurrentToken', $token);

        foreach ($tokens as $token) {

            // only process tokens that have attributes,
            //   namely start and empty tags
            if (!$token instanceof HTMLPurifier_Token_Start && !$token instanceof HTMLPurifier_Token_Empty) {
                continue;
            }

            // skip tokens that are armored
            if (!empty($token->armor['ValidateAttributes'])) {
                continue;
            }

            // note that we have no facilities here for removing tokens
            $validator->validateToken($token, $config, $context);
        }
        $context->destroy('CurrentToken');
        return $tokens;
    }
}

// vim: et sw=4 sts=4
