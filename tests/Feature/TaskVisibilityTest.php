<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\Subscriber;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserType;
use App\Models\WorkflowTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private UserType $superAdminType;
    private UserType $tenantAdminType;
    private UserType $regularUserType;
    private Division $divisionA;
    private Division $divisionB;
    private WorkflowTemplate $workflow;
    private Subscriber $subscriber;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user types
        $this->superAdminType = UserType::create([
            'name' => 'Super Admin',
            'description' => 'Super Administrator',
        ]);

        $this->tenantAdminType = UserType::create([
            'name' => 'Admin',
            'description' => 'Tenant Administrator',
        ]);

        $this->regularUserType = UserType::create([
            'name' => 'User',
            'description' => 'Regular User',
        ]);

        // Create tenant
        $this->tenant = Tenant::create([
            'id' => 'test-tenant',
            'name' => 'Test Tenant',
        ]);

        // Initialize tenant context
        tenancy()->initialize($this->tenant);

        // Create divisions
        $this->divisionA = Division::create([
            'name' => 'Division A',
            'tenant_id' => $this->tenant->id,
        ]);

        $this->divisionB = Division::create([
            'name' => 'Division B',
            'tenant_id' => $this->tenant->id,
        ]);

        // Create workflow template
        $this->workflow = WorkflowTemplate::create([
            'name' => 'Test Workflow',
            'description' => 'Test',
            'tenant_id' => $this->tenant->id,
            'industry_id' => 1,
            'created_by' => 1,
        ]);

        // Create subscriber
        $this->subscriber = Subscriber::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    /** @test */
    public function super_admin_can_see_all_tasks(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('password'),
            'user_type_id' => $this->superAdminType->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $userInDivisionA = User::create([
            'name' => 'User A',
            'email' => 'usera@test.com',
            'password' => bcrypt('password'),
            'user_type_id' => $this->regularUserType->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
        ]);

        // Create tasks in different divisions
        $taskInDivisionA = Task::create([
            'title' => 'Task in Division A',
            'workflow_template_id' => $this->workflow->id,
            'subscriber_id' => $this->subscriber->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
            'assigned_to' => $userInDivisionA->id,
            'status' => 'draft',
            'priority' => 'medium',
            'created_by' => $superAdmin->id,
        ]);

        $taskInDivisionB = Task::create([
            'title' => 'Task in Division B',
            'workflow_template_id' => $this->workflow->id,
            'subscriber_id' => $this->subscriber->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionB->id,
            'assigned_to' => $userInDivisionA->id,
            'status' => 'draft',
            'priority' => 'medium',
            'created_by' => $superAdmin->id,
        ]);

        // Super admin should see all tasks
        $visibleTasks = Task::visibleTo($superAdmin)->get();

        $this->assertCount(2, $visibleTasks);
        $this->assertTrue($visibleTasks->contains($taskInDivisionA));
        $this->assertTrue($visibleTasks->contains($taskInDivisionB));
    }

    /** @test */
    public function tenant_admin_can_see_all_tenant_tasks(): void
    {
        $tenantAdmin = User::create([
            'name' => 'Tenant Admin',
            'email' => 'admin@tenant.com',
            'password' => bcrypt('password'),
            'user_type_id' => $this->tenantAdminType->id,
            'tenant_id' => $this->tenant->id,
        ]);

        $userInDivisionA = User::create([
            'name' => 'User A',
            'email' => 'usera@test.com',
            'password' => bcrypt('password'),
            'user_type_id' => $this->regularUserType->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
        ]);

        // Create tasks in different divisions
        $taskInDivisionA = Task::create([
            'title' => 'Task in Division A',
            'workflow_template_id' => $this->workflow->id,
            'subscriber_id' => $this->subscriber->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
            'assigned_to' => $userInDivisionA->id,
            'status' => 'draft',
            'priority' => 'medium',
            'created_by' => $tenantAdmin->id,
        ]);

        $taskInDivisionB = Task::create([
            'title' => 'Task in Division B',
            'workflow_template_id' => $this->workflow->id,
            'subscriber_id' => $this->subscriber->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionB->id,
            'assigned_to' => $userInDivisionA->id,
            'status' => 'draft',
            'priority' => 'medium',
            'created_by' => $tenantAdmin->id,
        ]);

        // Tenant admin should see all tasks in their tenant
        $visibleTasks = Task::visibleTo($tenantAdmin)->get();

        $this->assertCount(2, $visibleTasks);
        $this->assertTrue($visibleTasks->contains($taskInDivisionA));
        $this->assertTrue($visibleTasks->contains($taskInDivisionB));
    }

    /** @test */
    public function regular_user_can_see_tasks_in_their_division(): void
    {
        $userInDivisionA = User::create([
            'name' => 'User A',
            'email' => 'usera@test.com',
            'password' => bcrypt('password'),
            'user_type_id' => $this->regularUserType->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
        ]);

        $userInDivisionB = User::create([
            'name' => 'User B',
            'email' => 'userb@test.com',
            'password' => bcrypt('password'),
            'user_type_id' => $this->regularUserType->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionB->id,
        ]);

        // Create task in Division A
        $taskInDivisionA = Task::create([
            'title' => 'Task in Division A',
            'workflow_template_id' => $this->workflow->id,
            'subscriber_id' => $this->subscriber->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
            'assigned_to' => $userInDivisionB->id,
            'status' => 'draft',
            'priority' => 'medium',
            'created_by' => $userInDivisionA->id,
        ]);

        // Create task in Division B
        $taskInDivisionB = Task::create([
            'title' => 'Task in Division B',
            'workflow_template_id' => $this->workflow->id,
            'subscriber_id' => $this->subscriber->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionB->id,
            'assigned_to' => $userInDivisionB->id,
            'status' => 'draft',
            'priority' => 'medium',
            'created_by' => $userInDivisionB->id,
        ]);

        // User A should only see task in Division A
        $visibleTasksForUserA = Task::visibleTo($userInDivisionA)->get();

        $this->assertCount(1, $visibleTasksForUserA);
        $this->assertTrue($visibleTasksForUserA->contains($taskInDivisionA));
        $this->assertFalse($visibleTasksForUserA->contains($taskInDivisionB));
    }

    /** @test */
    public function regular_user_can_see_tasks_assigned_to_them(): void
    {
        $userInDivisionA = User::create([
            'name' => 'User A',
            'email' => 'usera@test.com',
            'password' => bcrypt('password'),
            'user_type_id' => $this->regularUserType->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
        ]);

        $userInDivisionB = User::create([
            'name' => 'User B',
            'email' => 'userb@test.com',
            'password' => bcrypt('password'),
            'user_type_id' => $this->regularUserType->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionB->id,
        ]);

        // Create task in Division B but assigned to User A
        $taskAssignedToUserA = Task::create([
            'title' => 'Task assigned to User A',
            'workflow_template_id' => $this->workflow->id,
            'subscriber_id' => $this->subscriber->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionB->id,
            'assigned_to' => $userInDivisionA->id,
            'status' => 'draft',
            'priority' => 'medium',
            'created_by' => $userInDivisionB->id,
        ]);

        // User A should see the task even though it's in Division B
        $visibleTasks = Task::visibleTo($userInDivisionA)->get();

        $this->assertCount(1, $visibleTasks);
        $this->assertTrue($visibleTasks->contains($taskAssignedToUserA));
    }

    /** @test */
    public function regular_user_can_see_both_division_and_assigned_tasks(): void
    {
        $userInDivisionA = User::create([
            'name' => 'User A',
            'email' => 'usera@test.com',
            'password' => bcrypt('password'),
            'user_type_id' => $this->regularUserType->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
        ]);

        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@test.com',
            'password' => bcrypt('password'),
            'user_type_id' => $this->regularUserType->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
        ]);

        // Task in Division A assigned to other user
        $taskInDivision = Task::create([
            'title' => 'Task in Division A',
            'workflow_template_id' => $this->workflow->id,
            'subscriber_id' => $this->subscriber->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
            'assigned_to' => $otherUser->id,
            'status' => 'draft',
            'priority' => 'medium',
            'created_by' => $userInDivisionA->id,
        ]);

        // Task in Division B assigned to User A
        $taskAssignedToUser = Task::create([
            'title' => 'Task assigned to User A',
            'workflow_template_id' => $this->workflow->id,
            'subscriber_id' => $this->subscriber->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionB->id,
            'assigned_to' => $userInDivisionA->id,
            'status' => 'draft',
            'priority' => 'medium',
            'created_by' => $userInDivisionA->id,
        ]);

        // Task in Division A assigned to User A
        $taskBothConditions = Task::create([
            'title' => 'Task both in division and assigned',
            'workflow_template_id' => $this->workflow->id,
            'subscriber_id' => $this->subscriber->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
            'assigned_to' => $userInDivisionA->id,
            'status' => 'draft',
            'priority' => 'medium',
            'created_by' => $userInDivisionA->id,
        ]);

        // User A should see all three tasks
        $visibleTasks = Task::visibleTo($userInDivisionA)->get();

        $this->assertCount(3, $visibleTasks);
        $this->assertTrue($visibleTasks->contains($taskInDivision));
        $this->assertTrue($visibleTasks->contains($taskAssignedToUser));
        $this->assertTrue($visibleTasks->contains($taskBothConditions));
    }

    /** @test */
    public function regular_user_without_division_only_sees_assigned_tasks(): void
    {
        $userWithoutDivision = User::create([
            'name' => 'User Without Division',
            'email' => 'nodiv@test.com',
            'password' => bcrypt('password'),
            'user_type_id' => $this->regularUserType->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => null,
        ]);

        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@test.com',
            'password' => bcrypt('password'),
            'user_type_id' => $this->regularUserType->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
        ]);

        // Task in Division A assigned to other user
        $taskInDivision = Task::create([
            'title' => 'Task in Division A',
            'workflow_template_id' => $this->workflow->id,
            'subscriber_id' => $this->subscriber->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
            'assigned_to' => $otherUser->id,
            'status' => 'draft',
            'priority' => 'medium',
            'created_by' => $otherUser->id,
        ]);

        // Task assigned to user without division
        $taskAssignedToUser = Task::create([
            'title' => 'Task assigned to user',
            'workflow_template_id' => $this->workflow->id,
            'subscriber_id' => $this->subscriber->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
            'assigned_to' => $userWithoutDivision->id,
            'status' => 'draft',
            'priority' => 'medium',
            'created_by' => $otherUser->id,
        ]);

        // User without division should only see assigned task
        $visibleTasks = Task::visibleTo($userWithoutDivision)->get();

        $this->assertCount(1, $visibleTasks);
        $this->assertFalse($visibleTasks->contains($taskInDivision));
        $this->assertTrue($visibleTasks->contains($taskAssignedToUser));
    }

    /** @test */
    public function users_cannot_see_tasks_from_other_tenants(): void
    {
        $otherTenant = Tenant::create([
            'id' => 'other-tenant',
            'name' => 'Other Tenant',
        ]);

        $userInTenant1 = User::create([
            'name' => 'User Tenant 1',
            'email' => 'user1@test.com',
            'password' => bcrypt('password'),
            'user_type_id' => $this->regularUserType->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
        ]);

        $userInTenant2 = User::create([
            'name' => 'User Tenant 2',
            'email' => 'user2@test.com',
            'password' => bcrypt('password'),
            'user_type_id' => $this->regularUserType->id,
            'tenant_id' => $otherTenant->id,
        ]);

        // Create task in Tenant 1
        $taskInTenant1 = Task::create([
            'title' => 'Task in Tenant 1',
            'workflow_template_id' => $this->workflow->id,
            'subscriber_id' => $this->subscriber->id,
            'tenant_id' => $this->tenant->id,
            'division_id' => $this->divisionA->id,
            'assigned_to' => $userInTenant1->id,
            'status' => 'draft',
            'priority' => 'medium',
            'created_by' => $userInTenant1->id,
        ]);

        // User in Tenant 2 should not see task from Tenant 1
        $visibleTasks = Task::visibleTo($userInTenant2)->get();

        $this->assertCount(0, $visibleTasks);
        $this->assertFalse($visibleTasks->contains($taskInTenant1));
    }
}
