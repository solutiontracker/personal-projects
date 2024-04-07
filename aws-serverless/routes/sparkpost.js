const express = require('express')

const router = express.Router()

const sparkpost_controller = require('../webhook/sparkpost.controller');

const sqs_s3_controller = require('../webhook/sqs-s3-controller');

router.post('/sparkpost/webhook', sparkpost_controller.request);

router.post('/push-to-sqs', sqs_s3_controller.request);

module.exports = router