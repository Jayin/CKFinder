<?php


namespace Joinca\ZKUploader\Request\Transformer;

use Symfony\Component\HttpFoundation\Request;

/**
 * The JsonRequestTransformer class.
 *
 * Transforms POST requests based on the `jsonData` parameter.
 */
class JsonTransformer implements TransformerInterface
{
    /**
     * Transforms a request to the required format.
     *
     * @param Request $request the original request
     *
     * @return Request the request after the transformation
     */
    public function transform(Request $request)
    {
        // Transform only POST requests
        if (!$request->isMethod('POST')) {
            return $request;
        }

        // Transform only if POST request contains jsonData field
        $jsonData = $request->request->get('jsonData');

        if (null === $jsonData) {
            return $request;
        }

        $jsonParameters = json_decode((string) $jsonData, true);

        if (is_array($jsonParameters)) {
            $request->request->add($jsonParameters);
            $request->request->remove('jsonData');
        }

        return $request;
    }
}
