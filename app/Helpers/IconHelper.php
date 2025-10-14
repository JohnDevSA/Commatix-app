<?php

namespace App\Helpers;

class IconHelper
{
    /**
     * Get workflow template icon options organized by category.
     *
     * @return array<string, string>
     */
    public static function getWorkflowIcons(): array
    {
        return [
            // Business & Office
            '💼' => '💼 Briefcase',
            '📋' => '📋 Clipboard',
            '📊' => '📊 Chart',
            '📈' => '📈 Trending Up',
            '📉' => '📉 Trending Down',
            '🗂️' => '🗂️ Card Index',
            '📑' => '📑 Bookmark Tabs',
            '📄' => '📄 Page',
            '📃' => '📃 Page with Curl',
            '📝' => '📝 Memo',

            // Industry Specific
            '🏥' => '🏥 Hospital (Healthcare)',
            '⚖️' => '⚖️ Scales (Legal)',
            '🏦' => '🏦 Bank (Finance)',
            '🏭' => '🏭 Factory (Manufacturing)',
            '🏗️' => '🏗️ Construction',
            '🏪' => '🏪 Store (Retail)',
            '🎓' => '🎓 Education',
            '🍔' => '🍔 Food Service',
            '✈️' => '✈️ Travel',
            '🚚' => '🚚 Logistics',

            // Actions & Processes
            '🔍' => '🔍 Search/Review',
            '✅' => '✅ Approved',
            '⚡' => '⚡ Fast/Urgent',
            '🎯' => '🎯 Goal/Target',
            '🔄' => '🔄 Cycle/Recurring',
            '⚙️' => '⚙️ Settings/Config',
            '🔧' => '🔧 Tools/Maintenance',
            '📦' => '📦 Package/Delivery',
            '🎨' => '🎨 Creative/Design',
            '💡' => '💡 Ideas/Innovation',

            // Communication
            '📧' => '📧 Email',
            '📞' => '📞 Phone',
            '💬' => '💬 Chat',
            '📢' => '📢 Announcement',
            '📮' => '📮 Postbox',

            // Status & Priority
            '🔴' => '🔴 High Priority',
            '🟡' => '🟡 Medium Priority',
            '🟢' => '🟢 Low Priority',
            '⭐' => '⭐ Featured',
            '🏆' => '🏆 Achievement',

            // People & Teams
            '👤' => '👤 User',
            '👥' => '👥 Team',
            '🤝' => '🤝 Partnership',
            '👨‍💼' => '👨‍💼 Professional',

            // Security & Compliance
            '🔒' => '🔒 Secure',
            '🔐' => '🔐 Locked',
            '🛡️' => '🛡️ Protected',
            '✔️' => '✔️ Verified',
        ];
    }

    /**
     * Get milestone icon options organized by category.
     *
     * @return array<string, string>
     */
    public static function getMilestoneIcons(): array
    {
        return [
            // Completion & Approval
            '✅' => '✅ Completed',
            '✔️' => '✔️ Check',
            '👍' => '👍 Approved',
            '✓' => '✓ Done',
            '🎉' => '🎉 Celebration',
            '🏁' => '🏁 Finish',

            // Documentation
            '📝' => '📝 Write/Edit',
            '📄' => '📄 Document',
            '📋' => '📋 Form',
            '📑' => '📑 Files',
            '🗂️' => '🗂️ Archive',

            // Communication
            '📞' => '📞 Call',
            '📧' => '📧 Email',
            '💬' => '💬 Message',
            '📢' => '📢 Notify',
            '📮' => '📮 Send',

            // Review & Inspection
            '🔍' => '🔍 Review',
            '👀' => '👀 Inspect',
            '🔎' => '🔎 Examine',
            '📊' => '📊 Analyze',

            // People & Assignment
            '👤' => '👤 Assign User',
            '👥' => '👥 Assign Team',
            '🤝' => '🤝 Handoff',
            '👨‍💼' => '👨‍💼 Manager',

            // Status & Progress
            '⏳' => '⏳ In Progress',
            '⌛' => '⌛ Waiting',
            '🔄' => '🔄 Processing',
            '⏸️' => '⏸️ Paused',
            '⏹️' => '⏹️ Stopped',

            // Workflow Steps
            '1️⃣' => '1️⃣ Step 1',
            '2️⃣' => '2️⃣ Step 2',
            '3️⃣' => '3️⃣ Step 3',
            '4️⃣' => '4️⃣ Step 4',
            '5️⃣' => '5️⃣ Step 5',
            '6️⃣' => '6️⃣ Step 6',
            '7️⃣' => '7️⃣ Step 7',
            '8️⃣' => '8️⃣ Step 8',
            '9️⃣' => '9️⃣ Step 9',
            '🔟' => '🔟 Step 10',

            // Actions
            '📤' => '📤 Submit',
            '📥' => '📥 Receive',
            '📦' => '📦 Package',
            '🔧' => '🔧 Fix',
            '⚙️' => '⚙️ Configure',
            '🎯' => '🎯 Target',

            // Alerts & Priorities
            '⚠️' => '⚠️ Warning',
            '🚨' => '🚨 Alert',
            '❗' => '❗ Important',
            '⭐' => '⭐ Featured',
            '🔴' => '🔴 Critical',
            '🟡' => '🟡 Medium',
            '🟢' => '🟢 Low',

            // Generic
            '📌' => '📌 Pin',
            '🔖' => '🔖 Bookmark',
            '🎖️' => '🎖️ Badge',
            '🏆' => '🏆 Achievement',
            '💡' => '💡 Idea',
        ];
    }

    /**
     * Get all icon options (combined).
     *
     * @return array<string, string>
     */
    public static function getAllIcons(): array
    {
        return array_merge(
            self::getWorkflowIcons(),
            self::getMilestoneIcons()
        );
    }
}
