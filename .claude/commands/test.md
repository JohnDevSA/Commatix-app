---
description: Run tests and fix any failures
argument-hint: "[filter]"
---

Run the test suite for Commatix and fix any failures that occur.

If a filter argument is provided, run only tests matching that filter.

Steps:
1. Run `composer test` (which runs PHPUnit)
2. If there are failures, analyze the errors
3. Fix any failing tests
4. Re-run tests to confirm fixes
5. Report the test results

Make sure to:
- Check for syntax errors in test files
- Verify test assertions are correct
- Ensure database state is properly set up
- Fix any breaking changes in the codebase
