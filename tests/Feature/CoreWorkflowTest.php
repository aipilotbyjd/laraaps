<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowExecution;
use App\Services\Execution\ExecutionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CoreWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $executionService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test organization first
        $organization = \App\Models\Organization::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'name' => 'Test Organization',
            'slug' => 'test-org-'.\Illuminate\Support\Str::random(6),
            'email' => 'test@example.com',
            'plan' => 'free',
            'is_active' => true,
            'limits' => ['workflows' => 10, 'executions_per_month' => 1000, 'team_members' => 5],
        ]);

        // Create a test user with organization
        $this->user = User::factory()->create([
            'org_id' => $organization->id,
        ]);

        // Set user as organization owner
        $organization->update(['owner_id' => $this->user->id]);

        $this->executionService = new ExecutionService;

        // Mock external HTTP requests
        Http::fake([
            'jsonplaceholder.typicode.com/*' => Http::response(['id' => 1, 'title' => 'Test Post'], 200),
            '*' => Http::response(['success' => true], 200),
        ]);
    }

    /**
     * Test basic workflow execution with Start -> Set nodes
     */
    public function test_basic_workflow_execution()
    {
        $workflow = Workflow::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'name' => 'Basic Test Workflow',
            'org_id' => $this->user->org_id,
            'user_id' => $this->user->id,
            'nodes' => [
                [
                    'id' => 'start-1',
                    'type' => 'start',
                    'position' => ['x' => 0, 'y' => 0],
                    'properties' => [],
                ],
                [
                    'id' => 'set-1',
                    'type' => 'set',
                    'position' => ['x' => 100, 'y' => 0],
                    'properties' => [
                        'values' => [
                            ['key' => 'message', 'value' => 'Hello World'],
                            ['key' => 'status', 'value' => 'active'],
                        ],
                    ],
                ],
            ],
            'connections' => [
                ['source' => 'start-1', 'target' => 'set-1'],
            ],
        ]);

        $execution = $this->executionService->runWorkflow(
            $workflow->id,
            $this->user->org_id,
            $this->user->id,
            ['input' => 'test'],
            'test'
        );

        $this->assertEquals('success', $execution->status);
        $this->assertNotNull($execution->finished_at);
        $this->assertEquals(2, $execution->node_executions_count);
    }

    /**
     * Test conditional workflow with If node
     */
    public function test_conditional_workflow_execution()
    {
        $workflow = Workflow::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'name' => 'Conditional Workflow',
            'org_id' => $this->user->org_id,
            'user_id' => $this->user->id,
            'nodes' => [
                ['id' => 'start-1', 'type' => 'start'],
                [
                    'id' => 'if-1',
                    'type' => 'if',
                    'properties' => [
                        'conditions' => [
                            ['field' => 'age', 'operator' => '>', 'value' => 18],
                        ],
                    ],
                ],
                [
                    'id' => 'adult-path',
                    'type' => 'set',
                    'properties' => [
                        'values' => [['key' => 'category', 'value' => 'adult']],
                    ],
                ],
                [
                    'id' => 'minor-path',
                    'type' => 'set',
                    'properties' => [
                        'values' => [['key' => 'category', 'value' => 'minor']],
                    ],
                ],
            ],
            'connections' => [
                ['source' => 'start-1', 'target' => 'if-1'],
                ['source' => 'if-1', 'target' => 'adult-path', 'sourceHandle' => 'true'],
                ['source' => 'if-1', 'target' => 'minor-path', 'sourceHandle' => 'false'],
            ],
        ]);

        // Test with age > 18
        $execution = $this->executionService->runWorkflow(
            $workflow->id,
            $this->user->org_id,
            $this->user->id,
            ['age' => 25],
            'test'
        );

        $this->assertEquals('success', $execution->status);

        // Check that adult path was taken
        $nodeExecutions = $execution->nodeExecutions;
        $this->assertTrue($nodeExecutions->contains('node_id', 'adult-path'));
        $this->assertFalse($nodeExecutions->contains('node_id', 'minor-path'));
    }

    /**
     * Test HTTP Request node
     */
    public function test_http_request_node_execution()
    {
        $workflow = Workflow::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'name' => 'HTTP Request Workflow',
            'org_id' => $this->user->org_id,
            'user_id' => $this->user->id,
            'nodes' => [
                ['id' => 'start-1', 'type' => 'start'],
                [
                    'id' => 'http-1',
                    'type' => 'http-request',
                    'properties' => [
                        'url' => 'https://jsonplaceholder.typicode.com/posts/1',
                        'method' => 'GET',
                    ],
                ],
            ],
            'connections' => [
                ['source' => 'start-1', 'target' => 'http-1'],
            ],
        ]);

        $execution = $this->executionService->runWorkflow(
            $workflow->id,
            $this->user->org_id,
            $this->user->id,
            [],
            'test'
        );

        $this->assertEquals('success', $execution->status);

        // Verify HTTP request was made
        Http::assertSent(function ($request) {
            return $request->url() === 'https://jsonplaceholder.typicode.com/posts/1';
        });
    }

    /**
     * Test workflow with loop node
     */
    public function test_loop_node_execution()
    {
        $workflow = Workflow::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'name' => 'Loop Workflow',
            'org_id' => $this->user->org_id,
            'user_id' => $this->user->id,
            'nodes' => [
                ['id' => 'start-1', 'type' => 'start'],
                [
                    'id' => 'set-1',
                    'type' => 'set',
                    'properties' => [
                        'values' => [
                            ['key' => 'items', 'value' => [1, 2, 3, 4, 5]],
                        ],
                    ],
                ],
                [
                    'id' => 'loop-1',
                    'type' => 'loop',
                    'properties' => [
                        'items' => '{{items}}',
                    ],
                ],
            ],
            'connections' => [
                ['source' => 'start-1', 'target' => 'set-1'],
                ['source' => 'set-1', 'target' => 'loop-1'],
            ],
        ]);

        $execution = $this->executionService->runWorkflow(
            $workflow->id,
            $this->user->org_id,
            $this->user->id,
            [],
            'test'
        );

        $this->assertEquals('success', $execution->status);
    }

    /**
     * Test workflow error handling
     */
    public function test_workflow_handles_node_errors()
    {
        $workflow = Workflow::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'name' => 'Error Test Workflow',
            'org_id' => $this->user->org_id,
            'user_id' => $this->user->id,
            'nodes' => [
                ['id' => 'start-1', 'type' => 'start'],
                [
                    'id' => 'code-1',
                    'type' => 'code',
                    'properties' => [
                        'code' => 'throw new Exception("Test error");',
                    ],
                ],
            ],
            'connections' => [
                ['source' => 'start-1', 'target' => 'code-1'],
            ],
        ]);

        $execution = $this->executionService->runWorkflow(
            $workflow->id,
            $this->user->org_id,
            $this->user->id,
            [],
            'test'
        );

        $this->assertEquals('error', $execution->status);
        $this->assertNotNull($execution->error_message);
        $this->assertStringContainsString('Test error', $execution->error_message);
    }


    /**
     * Test concurrent workflow executions
     */
    public function test_concurrent_workflow_executions()
    {
        $workflow = Workflow::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'name' => 'Concurrent Test',
            'org_id' => $this->user->org_id,
            'user_id' => $this->user->id,
            'nodes' => [
                ['id' => 'start-1', 'type' => 'start'],
                ['id' => 'set-1', 'type' => 'set', 'properties' => [
                    'values' => [['key' => 'processed', 'value' => true]],
                ]],
            ],
            'connections' => [
                ['source' => 'start-1', 'target' => 'set-1'],
            ],
        ]);

        // Execute workflow multiple times concurrently
        $executions = [];
        for ($i = 0; $i < 5; $i++) {
            $executions[] = $this->executionService->runWorkflow(
                $workflow->id,
                $this->user->org_id,
                $this->user->id,
                ['iteration' => $i],
                'test'
            );
        }

        // Verify all executions completed
        foreach ($executions as $execution) {
            $this->assertEquals('success', $execution->status);
        }

        // Check total executions for this workflow
        $count = WorkflowExecution::where('workflow_id', $workflow->id)->count();
        $this->assertEquals(5, $count);
    }
}
