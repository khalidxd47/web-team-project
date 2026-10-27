---
name: token-saver
description: Enforce strict token-saving execution, minimal context loading, concise diffs, and zero conversational fluff.
---

# Token-Saving & Efficient Execution Guidelines

When this skill is active, strictly follow these constraints to minimize token usage:

1. **Direct File Writes Only**:
   - Do NOT output whole files or long code blocks into the terminal chat if you are writing directly to disk.
   - Summarize file changes in a single sentence (e.g., "Updated `events.php` with prepared statements").

2. **No Context Reloading**:
   - Do not re-read large files unless explicitly asked.
   - Target only the specific line numbers or functions that require modifications.

3. **Minimal Conversational Overhead**:
   - Eliminate greetings, preamble, explanations of standard library functions, and verbose summaries.
   - When asked to explain logic for a viva/discussion, provide strictly maximum 2 bullet points of under 20 words each.

4. **Batch Operations**:
   - Perform related edits, file creations, and Git actions in a single chained command without intermediate conversational turns.
