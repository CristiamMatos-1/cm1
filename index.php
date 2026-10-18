<?php
/**
 * Fallback quando o DocumentRoot é a raiz do projeto e o rewrite
 * não está disponível. Encaminha tudo para o front controller em Public/.
 */
require __DIR__ . '/Public/index.php';
