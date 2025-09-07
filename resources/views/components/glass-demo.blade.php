{{-- Glass Card Demo Component to verify styling --}}
<div class="space-y-6 p-6">
    {{-- Custom Glass Card (should have glass effect) --}}
    <div class="glass-card">
        <h3 class="text-lg font-semibold mb-2">Custom Glass Card</h3>
        <p>This should have glassmorphism styling with backdrop blur and transparency.</p>
    </div>

    {{-- Custom Glass Button (should have glass effect) --}}
    <button class="glass-button">
        Custom Glass Button
    </button>

    {{-- Tenant Card (should have glass effect with hover animation) --}}
    <div class="tenant-card">
        <h3 class="text-lg font-semibold mb-2">Tenant Card</h3>
        <p>This should have glassmorphism with hover rotation effect.</p>
    </div>

    {{-- Metric Card (should have glass effect with animated accent bar) --}}
    <div class="metric-card">
        <div class="metric-value">1,234</div>
        <div class="text-sm text-gray-600">Total Users</div>
        <div class="metric-trend-up">
            <span>↗</span>
            <span>12%</span>
        </div>
    </div>

    {{-- SA Business Card --}}
    <div class="sa-business-card">
        <h3 class="text-lg font-semibold mb-2">SA Business Card</h3>
        <p>This should have glassmorphism with a colored left border.</p>
    </div>

    {{-- Status badges --}}
    <div class="flex space-x-2">
        <span class="status-active compliance-badge">Active</span>
        <span class="status-trial compliance-badge">Trial</span>
        <span class="status-inactive compliance-badge">Inactive</span>
        <span class="status-suspended compliance-badge">Suspended</span>
    </div>

    {{-- B-BBEE Level badges --}}
    <div class="flex space-x-2">
        <span class="bee-level-1 compliance-badge">Level 1</span>
        <span class="bee-level-3 compliance-badge">Level 3</span>
        <span class="bee-non-compliant compliance-badge">Non-Compliant</span>
    </div>
</div>