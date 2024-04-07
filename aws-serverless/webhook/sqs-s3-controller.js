// Load the AWS SDK for Node.js
const AWS = require('aws-sdk');

const { SqsProducer } = require('sns-sqs-big-payload');

// Set the region we will be using
AWS.config.update({ region: 'us-west-1' });

exports.request = async (req, res) => {
    
    const data = req.body;

    const sqsProducer = SqsProducer.create({
        queueUrl: data.QueueUrl,
        region: 'eu-west-1',
        largePayloadThoughS3: true,
        s3Bucket: process.env.AWS_REQUEST_TO_SPEAK_BUCKET,
    });

    await sqsProducer.sendJSON(data, {
        MessageGroupId : data.MessageGroupId.toString(),
        MessageDeduplicationId : data.MessageDeduplicationId.toString()
    });

    return res.status(200).json({
        message: "Message send successfully."
    });

}