# Commatix Claude Code - Quick Start Guide

## 🎯 5-Minute Setup

### 1. Verify Installation
```bash
cd /home/johndevsa/projects/commatix
claude
```

### 2. Try Your First Commands

**Start development:**
```bash
/dev
```

**Run tests:**
```bash
/test
```

**Check code quality:**
```bash
/lint
```

### 3. Ask an Expert

**Laravel questions:**
```
@laravel-expert How do I implement a new service class following SOLID principles?
```

**Database design:**
```
@database-architect Help me design a schema for workflow versioning
```

**Testing help:**
```
@test-engineer Write tests for the WorkflowTemplateService
```

**API development:**
```
@api-developer Create a REST API endpoint for task management
```

## 📋 Essential Commands Cheat Sheet

| Command | Description | Example |
|---------|-------------|---------|
| `/dev` | Start all dev servers | `/dev` |
| `/test` | Run PHPUnit tests | `/test` or `/test WorkflowTest` |
| `/lint` | Code quality checks | `/lint` |
| `/migrate` | Run migrations | `/migrate` or `/migrate fresh` |
| `/filament-resource` | Create Filament CRUD | `/filament-resource Task` |
| `/cache-clear` | Clear all caches | `/cache-clear` |
| `/db-seed` | Seed database | `/db-seed` or `/db-seed UserSeeder` |
| `/deploy-check` | Pre-deployment check | `/deploy-check` |
| `/solid-review` | Review SOLID compliance | `/solid-review app/Services/WorkflowService.php` |
| `/tenant-test` | Test multi-tenancy | `/tenant-test` |
| `/blueprint` | Generate from YAML | `/blueprint` |
| `/queue-work` | Manage queues | `/queue-work start` |

## 🚀 Common Workflows

### Creating a New Feature
```
1. @database-architect I need a schema for [feature]
2. /blueprint
3. /filament-resource [ModelName]
4. @laravel-expert Help me implement [business logic]
5. @test-engineer Write tests for [feature]
6. /lint
7. /test
```

### Fixing a Bug
```
1. /cache-clear
2. /test
3. @laravel-expert [describe the bug]
4. /lint
5. /test
```

### Before Committing
```
1. /solid-review
2. /lint
3. /test
4. /deploy-check
```

## 🎓 Pro Tips

1. **Type `/` to see all commands** - Fuzzy search available
2. **Type `@` to see all agents** - Specialized AI assistants
3. **Use Tab for file completion** - When mentioning files
4. **Shift+Tab for auto-accept** - Toggle file edit confirmations
5. **Ctrl+R for transcript** - See full conversation history

## 📖 Full Documentation

- **Detailed Guide:** `.claude/README.md`
- **Project Context:** `CLAUDE.md` (project root)
- **Settings:** `.claude/settings.json`

## 🆘 Getting Help

**Command not working?**
1. Restart Claude Code
2. Check `.claude/commands/[command-name].md` exists
3. Verify frontmatter format

**Need more context?**
- Read `CLAUDE.md` for project overview
- Check `.claude/README.md` for full docs
- Use `@laravel-expert` for general questions

**MCP servers not loading?**
- Check `.mcp.json` configuration
- Run `claude mcp list` to verify
- Restart Claude Code

---

**Welcome to Commatix development!** 🎉

You now have access to:
- ✅ 12 specialized slash commands
- ✅ 4 expert AI agents
- ✅ 3 MCP servers for enhanced capabilities
- ✅ Optimized settings for Laravel/Filament development
- ✅ Comprehensive documentation

**Start coding with confidence!**
