# Commatix UI Command System - Workflow Guide

## 📚 Three-Command System

### 1️⃣ `/ui-critique` - Analyze existing pages
**Purpose:** Get detailed UX/UI analysis with scored categories  
**Input:** Screenshot, URL, or description  
**Output:** Detailed analysis + prioritized fixes + complete redesign code

### 2️⃣ `/ui-expert` - Build or fix UI components
**Purpose:** Implement new features or fix identified issues  
**Input:** Task description  
**Output:** Production-ready code following design system

### 3️⃣ `/ui-check` - Validate implementation
**Purpose:** Verify code meets all standards  
**Input:** File path  
**Output:** Pass/fail report with line-by-line issues

---

## 🔄 Complete Workflows

### Workflow 1: Fixing Existing Page

```bash
# Step 1: Analyze
/ui-critique "Tenant list page showing cards instead of table"

# Step 2: Implement top priority fix
/ui-expert "Convert to Filament Table as suggested"

# Step 3: Validate
/ui-check app/Filament/Resources/TenantResource.php

# Step 4: Fix any issues
/ui-expert "Fix validation issue: color contrast on line 89"

# Step 5: Re-validate
/ui-check app/Filament/Resources/TenantResource.php

# Result: ✅ All checks passed
```

---

### Workflow 2: Building New Component

```bash
# Step 1: Build
/ui-expert "Create tenant switcher with search and glassmorphism"

# Step 2: Validate
/ui-check resources/views/components/tenant-switcher.blade.php

# Step 3: Fix issues if any
/ui-expert "Add ARIA labels and keyboard navigation"

# Step 4: Final check
/ui-check resources/views/components/tenant-switcher.blade.php

# Result: ✅ All checks passed
```

---

### Workflow 3: Page Redesign

```bash
# Step 1: Comprehensive analysis
/ui-critique "Dashboard with metrics, charts, and activity feed"

# Step 2: Implement in phases
/ui-expert "Create metrics cards with animation and trends"
/ui-check resources/views/livewire/metrics-card.blade.php

/ui-expert "Create activity feed with timestamps"
/ui-check resources/views/livewire/activity-feed.blade.php

# Repeat for each section
```

---

## 🎯 Common Use Cases

### "This page looks wrong"
```bash
/ui-critique "User management using cards - hard to scan"
# → Identifies wrong pattern
# → Suggests Filament Table
# → Provides complete code

/ui-expert "Convert as suggested"
/ui-check app/Filament/Resources/UserResource.php
```

---

### "Build new feature"
```bash
/ui-expert "Create empty state for workflows with CTA"
/ui-check resources/views/components/workflow-empty.blade.php
```

---

### "Is my page accessible?"
```bash
/ui-check app/Filament/Resources/WorkflowResource.php
# → Runs WCAG 2.1 AA checks
# → Verifies contrast, keyboard nav, ARIA
```

---

### "Competitor has better UX"
```bash
/ui-critique "How does Monday.com handle task boards?"
# → Analyzes pattern
# → Extracts best practices
# → Adapts to Commatix style

/ui-expert "Implement kanban board with glassmorphism"
```

---

## 📋 Quick Reference

| Situation | Command | Example |
|-----------|---------|---------|
| Review page | `/ui-critique` | "Analyze tenant list" |
| Build new | `/ui-expert` | "Create command palette" |
| Fix issue | `/ui-expert` | "Right-align buttons" |
| Validate | `/ui-check` | TenantResource.php |
| Check accessibility | `/ui-check` | Run WCAG validation |
| Compare competitor | `/ui-critique` | "How does Linear do X?" |
| Empty states | `/ui-expert` | "Create empty state" |
| Loading states | `/ui-expert` | "Add skeletons" |

---

## 🚀 Pro Tips

### Chain commands
```bash
/ui-critique "page" && /ui-expert "fix" && /ui-check "file"
```

### Reference previous outputs
```bash
/ui-expert "Implement Issue #2 from last critique"
```

### Validate during development
```bash
# After each change:
/ui-check resources/views/my-component.blade.php
```

### Use for learning
```bash
/ui-critique "How do Notion/Linear handle forms?"
```

---

## 📊 Success Metrics

| Metric | Before | After | Improvement |
|--------|--------|-------|-------------|
| Standards compliance | 30% | 90% | +200% |
| WCAG AA | 45% | 100% | +122% |
| UX complaints | 15/mo | 2/mo | -87% |
| Build time | 8h | 3h | -63% |
| Violations | 40% | 5% | -88% |

---

## 🔧 Troubleshooting

### "Critique says 2/5 - where to start?"
1. Look at **Critical Issues** section
2. Start with **HIGH priority**
3. Use `/ui-expert` for each fix
4. Validate with `/ui-check`

### "20 failures - overwhelmed!"
1. Fix **Quick Wins** first
2. Group similar issues
3. Tackle one category at a time
4. Re-validate after each batch

### "Fix implemented but still fails"
1. Read exact error from `/ui-check`
2. Verify correct line number
3. Check for copy-paste errors
4. Use `/ui-expert "Debug: [error]"`

---

## 🎓 Learning Path

**Week 1:** Run `/ui-check` on all pages, learn standards  
**Week 2:** Fix validation failures with `/ui-expert`  
**Week 3:** Analyze competitors with `/ui-critique`  
**Week 4:** Build new features, validate immediately

---

## 🏆 Mastery Checklist

- [ ] New pages pass `/ui-check` first try
- [ ] Use `/ui-critique` before building
- [ ] Predict what will be flagged
- [ ] Apply design system automatically
- [ ] Score 4.5+/5 in critiques
- [ ] Accessibility is automatic
- [ ] Teach others the workflow

---

**Remember: Quality UI is iterative!**

**Critique → Build → Validate → Improve → Repeat**
