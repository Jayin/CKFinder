<?php


namespace Joinca\ZKUploader\Command;

use Joinca\ZKUploader\Cache\CacheManager;
use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Filesystem\Folder\WorkingFolder;
use Joinca\ZKUploader\Config;
use Joinca\ZKUploader\Response\JsonResponse;
use Joinca\ZKUploader\Thumbnail\ThumbnailRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class QuickUpload extends FileUpload
{
    public function __construct(ZKUploader $app)
    {
        parent::__construct($app);

        $app->on(KernelEvents::RESPONSE, array($this, 'onQuickUploadResponse'));
    }

    public function execute(Request $request, WorkingFolder $workingFolder, EventDispatcher $dispatcher, Config $config, CacheManager $cache, ThumbnailRepository $thumbsRepository)
    {
        // Don't add info about current folder to this command response
        $workingFolder->omitResponseInfo();

        $responseData = parent::execute($request, $workingFolder, $dispatcher, $config, $cache, $thumbsRepository);

        // Get url to a file
        if (isset($responseData['fileName'])) {
            $responseData['url'] = $workingFolder->getFileUrl($responseData['fileName']);
        }

        return $responseData;
    }

    public function onQuickUploadResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->get('responseType') === 'json') {
            return;
        }

        $response = $event->getResponse();

        $funcNum = (string) $request->get('CKEditorFuncNum');
        $funcNum = preg_replace('/[^0-9]/', '', $funcNum);

        if ($response instanceof JsonResponse) {
            $responseData = $response->getData();

            $fileUrl = isset($responseData['url']) ? $responseData['url'] : '';
            $errorMessage = isset($responseData['error']['message']) ? $responseData['error']['message'] : '';

            ob_start();
            ?>
<script type="text/javascript">
    window.parent.CKEDITOR.tools.callFunction(<?php echo json_encode($funcNum); ?>, <?php echo json_encode($fileUrl); ?>, <?php echo json_encode($errorMessage); ?>);
</script>
            <?php

            $event->setResponse(Response::create(ob_get_clean()));
        }
    }
}
