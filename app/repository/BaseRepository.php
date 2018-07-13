<?php

namespace SbDaRepository;

use Phalcon\Di as di;


class BaseRepository
{

    public function __construct()
    {
        $di = di::getDefault();
        $this->db = $di->get('db');
        $this->config = $di->get('config');
        $this->session = $di->get('session');
        $this->url = $di->get('url');
        $this->modelsMetadata = $di->get('modelsMetadata');
        $this->redis = $di->get('redis');
        $this->jwt = $this->config->jwt;
        $this->signConfig = $this->config->signature;
        $this->appKey = $this->config->signature->appkey;
    }
}