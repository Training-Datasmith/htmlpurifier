# HTMLPurifier Architecture

## Purpose

HTMLPurifier is a standards-compliant HTML filter library that removes XSS vectors and malformed markup by parsing, validating against a strict HTML definition, and regenerating clean output. It is **not** a simple regex-based sanitiser — it uses a full HTML-aware token stream pipeline.

## Directory Structure

```
library/HTMLPurifier/
  HTMLPurifier.php          — Top-level entry point: purify($html, $config)
  Config.php                — Configuration object (HTML.Allowed, Core.*, URI.*, CSS.*, etc.)
  Context.php               — Per-purification shared state bag (current token, ID accumulator, …)
  Lexer/
    DirectLex.php           — Regex-based tokeniser (fast, handles malformed HTML)
    DOMLex.php              — DOM-based tokeniser (more accurate, requires ext/dom)
  Strategy/
    RemoveForeignElements.php — Strips tags not in the HTML definition; transforms legacy tags
    ValidateAttributes.php  — Removes and normalises attributes against the definition
    MakeWellFormed.php      — Inserts/removes tokens to produce well-nested markup
    FixNesting.php          — Enforces element content-model constraints
    Composite.php           — Runs all four strategies in sequence
  AttrDef/                  — One class per attribute type (URI, Color, Enum, CSS, …)
  AttrTransform/            — Pre/post attribute transformation rules
  Injector/
    AutoParagraph.php       — Wraps bare text in <p>
    Linkify.php             — Converts plain URLs to <a> links
  Generator.php             — Serialises the cleaned token stream back to HTML string
  HTMLDefinition.php        — Registry of allowed elements, attributes, and content models
  HTMLModuleManager.php     — Assembles HTMLDefinition from swappable HTML modules
  HTMLModule/               — Per-spec feature modules (Hypertext, Forms, Scripting, …)
  URIFilter/                — Pluggable URI validators and transformers
  Filter/                   — Pre/post whole-document filters (ExtractStyleBlocks, YouTube)
  DefinitionCache/          — Serialised definition cache (filesystem or null)
  Exception.php             — Base exception
```

## Purification Pipeline

```
Input HTML string
    ↓ Lexer (DirectLex or DOMLex)
Token stream
    ↓ Strategy\RemoveForeignElements   — XSS layer 1: strip unknown tags
    ↓ Strategy\MakeWellFormed          — repair nesting
    ↓ Strategy\FixNesting              — enforce content models
    ↓ Strategy\ValidateAttributes      — XSS layer 2: strip dangerous attributes/URIs
    ↓ Generator
Clean HTML string
```

## Key Design Decisions

- **Whitelist, not blacklist**: Only explicitly allowed elements and attributes pass through. Unknown anything is dropped.
- **Attribute definitions**: Every attribute has a typed validator (`AttrDef\*`). URIs go through a separate `URIScheme` whitelist (e.g., only `http`, `https`, `mailto` by default) and `URIFilter` chain.
- **No `eval`, no `javascript:` URIs**: The URI validator strips `javascript:`, `vbscript:`, `data:` (unless explicitly enabled), and other non-whitelisted schemes.
- **Configurable trust levels**: `HTML.Trusted = true` allows comments and some additional elements for trusted content rendering.
- **Definition caching**: Parsed HTML definitions are serialised to disk to avoid repeated parsing overhead on each request.

## Extension Points

- **Custom elements**: Call `$config->getHTMLDefinition(true)->addElement(...)` to add elements to the whitelist.
- **Custom attributes**: Use `addAttribute()` or create a new `AttrDef` subclass.
- **Custom URI filters**: Implement `URIFilter` and register with `$config->set('URI.DefinitionID', ...)`.
- **Pre/post filters**: Extend `Filter` and add via `$config->set('Filter.Custom', [...])`.
- **Custom injectors**: Extend `Injector` for token-stream transformations (e.g., auto-linking).

## Dependency Flow

```
HTMLPurifier (entry point)
    └─ Config → HTMLDefinition (via HTMLModuleManager)
    └─ Lexer → Token stream
    └─ Strategy\Composite
          ├─ RemoveForeignElements → HTMLDefinition, AttrValidator
          ├─ MakeWellFormed        → Injectors
          ├─ FixNesting            → HTMLDefinition content models
          └─ ValidateAttributes   → AttrDef instances, URIFilter chain
    └─ Generator → output string
```
