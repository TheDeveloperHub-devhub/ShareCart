<?php

declare(strict_types=1);

namespace DeveloperHub\ShareCart\Api\Data;

interface ShareCartInterface
{
    const ID = "entity_id";
    const PRODUCT_DATA = "product_data";
    const HASH = "hash";

    /** @return mixed */
    public function getId();

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /** @return mixed */
    public function getProductData();

    /**
     * @param $data
     * @return mixed
     */
    public function setProductData($data);

    /** @return mixed */
    public function getHash();

    /**
     * @param $hash
     * @return mixed
     */
    public function setHash($hash);
}
