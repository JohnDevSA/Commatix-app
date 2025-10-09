---
description: Run code quality checks and fix issues
---

Run all code quality checks for Commatix and automatically fix issues where possible.

Execute the following in order:
1. **Laravel Pint**: Run `composer lint:fix` to auto-fix code style
2. **PHPStan**: Run `composer lint` to check for type errors
3. **GrumPHP**: Run `composer grumphp` to verify git hooks pass

For each tool:
- Show the output
- If there are errors, explain them clearly
- Fix automatically fixable issues
- For manual fixes, explain what needs to be changed and why

Focus on:
- PSR-12 code style compliance
- Type safety and PHPStan level issues
- Code duplication (phpcpd)
- Best practices violations
