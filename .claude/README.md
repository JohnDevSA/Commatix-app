# Claude Code Configuration for Commatix

This directory contains **enterprise-grade Claude Code configuration** for the Commatix multi-tenant workflow platform. This setup is designed to accelerate development for current and future team members.

## 📁 Directory Structure

```
.claude/
├── README.md              # This file - complete documentation
├── settings.json          # Project-specific Claude Code settings
├── commands/              # Custom slash commands (12 commands)
│   ├── test.md           # Run tests and fix failures
│   ├── lint.md           # Code quality checks
│   ├── filament-resource.md  # Generate Filament resources
│   ├── migrate.md        # Database migrations
│   ├── dev.md            # Start development servers
│   ├── tenant-test.md    # Test multi-tenancy
│   ├── cache-clear.md    # Clear all caches
│   ├── solid-review.md   # SOLID principles review
│   ├── blueprint.md      # Generate code from Blueprint
│   ├── deploy-check.md   # Pre-deployment validation
│   ├── db-seed.md        # Seed database
│   └── queue-work.md     # Queue management
└── agents/                # Custom AI agents (4 specialists)
    ├── laravel-expert.md      # Laravel & Filament specialist
    ├── database-architect.md  # Database design expert
    ├── test-engineer.md       # Testing specialist
    └── api-developer.md       # API & integration expert
```

## 🚀 Quick Start

### For New Team Members

1. **Install Claude Code**
   ```bash
   npm install -g @anthropic-ai/claude-code
   ```

2. **Navigate to Project**
   ```bash
   cd /path/to/commatix
   ```

3. **Start Claude Code**
   ```bash
   claude
   ```

4. **Available Commands** (type `/` to see all)
   - `/dev` - Start development environment
   - `/test` - Run test suite
   - `/lint` - Check code quality
   - `/filament-resource User` - Create Filament resource
   - `/migrate` - Run database migrations
   - `/help` - See all available commands

### For Experienced Developers

Use custom agents for specialized tasks:
- `@laravel-expert` - Laravel/Filament questions
- `@database-architect` - Database schema design
- `@test-engineer` - Testing and quality assurance
- `@api-developer` - API development

## 📋 Custom Slash Commands

### Development Workflow

#### `/dev`
Start all development servers (Laravel, Queue Worker, Vite)
```bash
/dev
```

#### `/test [filter]`
Run PHPUnit tests, optionally filter by test name
```bash
/test
/test WorkflowTest
```

#### `/lint`
Run code quality checks (Pint, PHPStan, GrumPHP)
```bash
/lint
```

### Database Operations

#### `/migrate [fresh|refresh|rollback]`
Run database migrations with safety checks
```bash
/migrate              # Run pending migrations
/migrate fresh        # ⚠️ Drop all tables and re-migrate
/migrate refresh      # ⚠️ Rollback and re-migrate
/migrate rollback     # Rollback last batch
```

#### `/db-seed [SeederClass]`
Seed database with sample data
```bash
/db-seed              # Run all seeders
/db-seed UserSeeder   # Run specific seeder
```

### Filament Resources

#### `/filament-resource <ModelName>`
Generate complete Filament CRUD resource
```bash
/filament-resource Task
/filament-resource Subscriber
```

### Code Quality & Architecture

#### `/solid-review [file-path]`
Review code for SOLID principles compliance
```bash
/solid-review
/solid-review app/Services/WorkflowService.php
```

#### `/blueprint [draft-file]`
Generate Laravel code from Blueprint YAML
```bash
/blueprint
/blueprint custom-draft.yaml
```

### Multi-Tenancy

#### `/tenant-test`
Test multi-tenancy functionality and data isolation
```bash
/tenant-test
```

### Deployment

#### `/deploy-check`
Run comprehensive pre-deployment checklist
```bash
/deploy-check
```

### Utilities

#### `/cache-clear`
Clear all application caches (config, route, view, etc.)
```bash
/cache-clear
```

#### `/queue-work [start|status|failed|retry]`
Manage queue workers and background jobs
```bash
/queue-work start    # Start queue worker
/queue-work status   # Check queue status
/queue-work failed   # List failed jobs
/queue-work retry    # Retry failed jobs
```

## 🤖 Custom AI Agents

Agents are specialized AI assistants with deep expertise in specific areas.

### @laravel-expert
**Expertise:** Laravel 12, Filament 3, Multi-tenancy, SOLID principles

Use for:
- Implementing new features
- Reviewing Laravel code
- Filament resource development
- Multi-tenant architecture questions
- Service class design

```
@laravel-expert How should I structure a new WorkflowExecutionService?
```

### @database-architect
**Expertise:** MySQL, Laravel migrations, Schema design, Performance optimization

Use for:
- Designing database schemas
- Creating migrations
- Optimizing queries
- Adding indexes
- Multi-tenant database strategies

```
@database-architect Design a schema for workflow versioning
```

### @test-engineer
**Expertise:** PHPUnit, Laravel testing, Code quality, GrumPHP

Use for:
- Writing tests
- Improving test coverage
- Code quality issues
- Static analysis problems
- Pre-commit hook setup

```
@test-engineer Write tests for the TaskSchedulingService
```

### @api-developer
**Expertise:** REST APIs, Laravel Sanctum, Resend, Vonage, Webhooks

Use for:
- Building API endpoints
- Integration with external services
- Email/SMS functionality
- API documentation
- Webhook handling

```
@api-developer Create an API endpoint for workflow execution
```

## ⚙️ Settings Configuration

### Allowed Tools (Auto-approved)
Claude can automatically use these tools without asking:
- Read PHP files in `app/`, `database/`, `tests/`, `config/`
- Read documentation and config files
- Run `composer` commands
- Run `php artisan` commands
- Run `npm`/`pnpm` commands
- Git read operations (`status`, `diff`, `log`)

### Disallowed Tools (Blocked)
These dangerous operations are blocked:
- `rm -rf *` - Recursive delete
- Editing `.env` file - Prevent secret exposure
- `git push --force` - Prevent force push
- `php artisan migrate:fresh` - Prevent data loss
- `php artisan db:wipe` - Prevent data loss

### Ignored Patterns
These directories are excluded from context (performance):
- `vendor/` - Composer dependencies
- `node_modules/` - NPM dependencies
- `storage/` - Logs and cache
- `.git/` - Git internals
- `public/build/` - Compiled assets

## 🎯 Workflow Examples

### Creating a New Feature

```bash
# 1. Design the database schema
@database-architect I need tables for workflow versioning

# 2. Generate migrations and models via Blueprint
/blueprint

# 3. Create Filament resources
/filament-resource WorkflowVersion

# 4. Implement business logic
@laravel-expert Help me create WorkflowVersioningService

# 5. Write tests
@test-engineer Write comprehensive tests for versioning

# 6. Check code quality
/lint

# 7. Run tests
/test

# 8. Pre-deployment check
/deploy-check
```

### Debugging an Issue

```bash
# 1. Clear caches (often solves issues)
/cache-clear

# 2. Check test suite
/test

# 3. Review code quality
/lint

# 4. Test multi-tenancy if relevant
/tenant-test

# 5. Check queue jobs
/queue-work status
/queue-work failed
```

### Daily Development Routine

```bash
# Morning: Start development
/dev                  # Start servers

# During development
/test                 # Run tests frequently
@laravel-expert       # Ask questions
/lint                 # Check code quality

# Before committing
/solid-review         # Review architecture
/lint                 # Final quality check
/test                 # Ensure tests pass

# End of day
/deploy-check         # Ensure deployment readiness
```

## 🔧 MCP Servers

MCP (Model Context Protocol) servers provide enhanced capabilities:

### Browser MCP
- **Purpose:** Test Commatix UI in browser
- **Port:** 8000 (matches Laravel server)
- **Usage:** Automated UI testing, screenshot debugging

### Filesystem MCP
- **Purpose:** Enhanced file operations
- **Scope:** Commatix codebase only
- **Usage:** Advanced file search and manipulation

### Git MCP
- **Purpose:** Git operations and history
- **Scope:** Commatix repository
- **Usage:** Advanced git operations, history analysis

## 📚 Best Practices

### For Individual Developers

1. **Use slash commands** for common tasks instead of typing full commands
2. **Invoke agents** with `@` for specialized expertise
3. **Run `/lint`** before committing code
4. **Run `/test`** after making changes
5. **Use `/solid-review`** for architectural decisions
6. **Clear caches** with `/cache-clear` when seeing stale data

### For Team Collaboration

1. **Share command patterns** - Document useful command sequences
2. **Update agents** when domain knowledge grows
3. **Add new commands** for repeated tasks
4. **Review settings** together as team evolves
5. **Maintain this README** as single source of truth

### Code Quality Standards

This configuration enforces:
- ✅ PSR-12 code style (Laravel Pint)
- ✅ PHPStan level 8 static analysis
- ✅ No code duplication (PHPCPD)
- ✅ GrumPHP pre-commit hooks
- ✅ Comprehensive test coverage
- ✅ SOLID principles compliance

## 🚨 Security Considerations

### What Claude CAN'T Do (by design)
- Modify `.env` files (secrets are protected)
- Run destructive database commands without confirmation
- Force push to git
- Delete files recursively
- Access production databases directly

### What to Review Manually
- Database migrations (always review before running)
- Third-party package updates
- Git merge/rebase operations
- Production deployments

## 🆘 Troubleshooting

### "Permission denied" errors
Some operations require confirmation. This is intentional for:
- Database migrations
- Destructive operations
- Git operations that rewrite history

### Commands not appearing
1. Restart Claude Code
2. Check file naming in `.claude/commands/` (must be `.md`)
3. Check frontmatter format

### Agents not responding
1. Ensure agent files are in `.claude/agents/`
2. Restart Claude Code
3. Check agent file syntax

### Slow performance
1. Check `ignorePatterns` in settings.json
2. Run `/cache-clear`
3. Reduce context by specifying file paths

## 📈 Future Enhancements

Consider adding:
- [ ] Pre-commit hooks integration
- [ ] Custom output styles for different contexts
- [ ] Additional agents (DevOps, Security, etc.)
- [ ] More specialized commands for Commatix workflows
- [ ] Team-specific shortcuts and aliases

## 📖 Additional Resources

- [Claude Code Documentation](https://docs.claude.com/en/docs/claude-code)
- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Filament 3 Documentation](https://filamentphp.com/docs/3.x)
- [Stancl Tenancy Documentation](https://tenancyforlaravel.com/docs/v3)
- [Commatix Internal Wiki](#) *(add your wiki link here)*

## 🤝 Contributing to This Configuration

Team members are encouraged to improve this setup:

1. **Add commands** for repeated workflows
2. **Enhance agents** with project-specific knowledge
3. **Update documentation** when patterns change
4. **Share discoveries** in team meetings
5. **Propose improvements** via pull requests

---

**Last Updated:** 2025-10-10
**Maintained By:** Development Team
**Questions?** Ask in #development Slack channel or consult the team lead

**Pro Tip:** Type `/` in Claude Code to see all available commands, and `@` to invoke custom agents!
