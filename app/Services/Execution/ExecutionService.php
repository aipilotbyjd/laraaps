<?php

namespace App\Services\Execution;

use App\Jobs\ExecuteWorkflowJob;
use App\Models\Node;
use App\Models\Workflow;
use App\Models\WorkflowExecution;
use App\Services\Node\Execution\NodeExecutorFactory;
use App\Services\Workflow\Graph;
use Illuminate\Support\Str;

class ExecutionService
{
    public function getExecutionsByOrg(string $orgId)
    {
        return WorkflowExecution::where('org_id', $orgId)->get();
    }

    public function getExecution(string $id): ?WorkflowExecution
    {
        return WorkflowExecution::find($id);
    }

    public function deleteExecution(string $id): bool
    {
        $execution = WorkflowExecution::find($id);

        if (! $execution) {
            return false;
        }

        return $execution->delete();
    }

    public function bulkDeleteExecutions(array $executionIds): void
    {
        WorkflowExecution::whereIn('id', $executionIds)->delete();
    }

    public function executeWorkflow(string $workflowId, string $orgId, string $userId, array $triggerData, string $mode): void
    {
        ExecuteWorkflowJob::dispatch($workflowId, $orgId, $userId, $triggerData, $mode);
    }

    public function runWorkflow(string $workflowId, string $orgId, string $userId, array $triggerData, string $mode): WorkflowExecution
    {
        $workflow = Workflow::find($workflowId);
        $workflowExecution = WorkflowExecution::create([
            'id' => Str::uuid(),
            'workflow_id' => $workflowId,
            'org_id' => $orgId,
            'user_id' => $userId,
            'trigger_data' => $triggerData,
            'mode' => $mode,
            'status' => 'running',
        ]);

        $nodes = collect($workflow->nodes);
        $connections = collect($workflow->connections);

        $graph = new Graph;
        foreach ($nodes as $node) {
            $graph->addNode($node['id']);
        }
        foreach ($connections as $connection) {
            $graph->addEdge($connection['source'], $connection['target']);
        }

        $startNode = $nodes->firstWhere('type', 'start');

        try {
            $this->executeNode($startNode['id'], $triggerData, $workflowExecution, $nodes, $connections, $graph);
            $workflowExecution->status = 'success';
        } catch (\Exception $e) {
            $workflowExecution->status = 'error';
            $workflowExecution->error_message = $e->getMessage();
        }

        $workflowExecution->save();

        return $workflowExecution;
    }

    private function executeNode(string $nodeId, array $inputData, WorkflowExecution $workflowExecution, $nodes, $connections, Graph $graph)
    {
        $currentNode = $nodes->firstWhere('id', $nodeId);
        $nodeModel = new Node($currentNode);

        $executor = NodeExecutorFactory::make($nodeModel, $workflowExecution);
        $outputData = $executor->execute($inputData);

        $successors = $graph->getSuccessors($nodeId);

        if ($nodeModel->type === 'if') {
            $branch = $outputData['__branch'] ?? 'false';
            $data = $outputData['data'];

            $nextConnection = $connections->firstWhere(function ($connection) use ($nodeId, $branch) {
                return $connection['source'] === $nodeId && $connection['sourceHandle'] === $branch;
            });

            if ($nextConnection) {
                $this->executeNode($nextConnection['target'], $data, $workflowExecution, $nodes, $connections, $graph);
            }
        } else {
            foreach ($successors as $successorId) {
                $this->executeNode($successorId, $outputData, $workflowExecution, $nodes, $connections, $graph);
            }
        }
    }

    // Mocked methods for now

    public function stop(string $id)
    {
        return ['message' => 'Execution stopped.'];
    }

    public function retry(string $id)
    {
        return ['message' => 'Execution retried.'];
    }

    public function resume(string $id)
    {
        return ['message' => 'Execution resumed.'];
    }

    public function bulkRetry(array $ids)
    {
        return ['message' => 'Executions retried.'];
    }

    public function getNodes(string $id)
    {
        return [];
    }

    public function getNode(string $id, string $nodeId)
    {
        return [];
    }

    public function getLogs(string $id)
    {
        return [];
    }

    public function getTimeline(string $id)
    {
        return [];
    }

    public function getData(string $id)
    {
        return [];
    }

    public function getErrors(string $id)
    {
        return [];
    }

    public function getWaiting()
    {
        return [];
    }

    public function continueWaiting(string $id)
    {
        return ['message' => 'Execution continued.'];
    }

    public function cancelWaiting(string $id)
    {
        return ['message' => 'Waiting execution cancelled.'];
    }

    public function getStats()
    {
        return [];
    }

    public function getDailyStats()
    {
        return [];
    }

    public function getStatsByWorkflow()
    {
        return [];
    }

    public function getStatsByStatus()
    {
        return [];
    }

    public function getPerformanceStats()
    {
        return [];
    }

    public function getQueueStatus()
    {
        return [];
    }

    public function getQueueMetrics()
    {
        return [];
    }

    public function clearQueue()
    {
        return ['message' => 'Queue cleared.'];
    }

    public function setQueuePriority(string $id, int $priority)
    {
        return ['message' => 'Queue priority set.'];
    }
}
