<?php



namespace Joinca\ZKUploader\Command;

use Symfony\Component\HttpFoundation\Request;

class Operation extends CommandAbstract
{
    public function execute(Request $request)
    {
        $operationId = (string) $request->query->get('operationId');

        /* @var \Joinca\ZKUploader\Operation\OperationManager $operation */
        $operation = $this->app['operation'];

        if ($request->query->get('abort')) {
            $operation->abort($operationId);
        }

        return $operation->getStatus($operationId);
    }
}
