<?php

declare (strict_types=1);
/**
 * Converts a stream of HTMLPurifier_Token into an HTMLPurifier_Node,
 * and back again.
 *
 * @note This transformation is not an equivalence.  We mutate the input
 * token stream to make it so; see all [MUT] markers in code.
 */
class Html_Purifier_arborize
{
    public static function arborize($tokens, $config, $context)
    {
        $definition = $config->get_html_definition();
        $parent = new Html_Purifier_token_start($definition->info_parent);
        $stack = [$parent->to_node()];
        foreach ($tokens as $token) {
            $token->skip = null;
            // [MUT]
            $token->carryover = null;
            // [MUT]
            if ($token instanceof Html_Purifier_token_end) {
                $token->start = null;
                // [MUT]
                $r = array_pop($stack);
                //assert($r->name === $token->name);
                //assert(empty($token->attr));
                $r->end_col = $token->col;
                $r->end_line = $token->line;
                $r->end_armor = $token->armor;
                continue;
            }
            $node = $token->to_node();
            $stack[count($stack) - 1]->children[] = $node;
            if ($token instanceof Html_Purifier_token_start) {
                $stack[] = $node;
            }
        }
        //assert(count($stack) == 1);
        return $stack[0];
    }
    public static function flatten($node, $config, $context)
    {
        $level = 0;
        $nodes = [$level => new Html_Purifier_queue([$node])];
        $closing_tokens = [];
        $tokens = [];
        do {
            while (!$nodes[$level]->is_empty()) {
                $node = $nodes[$level]->shift();
                // FIFO
                list($start, $end) = $node->to_token_pair();
                if ($level > 0) {
                    $tokens[] = $start;
                }
                if ($end !== null) {
                    $closing_tokens[$level][] = $end;
                }
                if ($node instanceof Html_Purifier_node_element) {
                    $level++;
                    $nodes[$level] = new Html_Purifier_queue();
                    foreach ($node->children as $child_node) {
                        $nodes[$level]->push($child_node);
                    }
                }
            }
            $level--;
            if ($level && isset($closing_tokens[$level])) {
                while ($token = array_pop($closing_tokens[$level])) {
                    $tokens[] = $token;
                }
            }
        } while ($level > 0);
        return $tokens;
    }
}