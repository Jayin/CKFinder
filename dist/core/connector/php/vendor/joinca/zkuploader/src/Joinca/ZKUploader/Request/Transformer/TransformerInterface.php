<?php


namespace Joinca\ZKUploader\Request\Transformer;

use Symfony\Component\HttpFoundation\Request;

/**
 * The TransformerInterface interface.
 *
 * Request transformers transform any kind of HTTP request to a request
 * format understandable by the ZKUploader connector.
 */
interface TransformerInterface
{
    /**
     * Transforms a request to the required format.
     *
     * @param Request $request the original request
     *
     * @return Request the request after the transformation
     */
    public function transform(Request $request);
}
