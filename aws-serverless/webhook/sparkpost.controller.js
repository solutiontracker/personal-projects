// Load the AWS SDK for Node.js
const AWS = require('aws-sdk');

// Set the region we will be using
AWS.config.update({ region: 'us-west-1' });

// Create SQS service client
const sqs = new AWS.SQS({ apiVersion: '2012-11-05' });

exports.request = async (req, res) => {

    const payload = req.body;

    if(payload) {
        const params = {
            MessageBody: JSON.stringify(payload),
            QueueUrl: `https://sqs.eu-west-1.amazonaws.com/${process.env.AWS_ACCOUNT_ID}/${process.env.AWS_SPARKPOST_QUEUE}`
        };

        sqs.sendMessage(params, (err, data) => {
            if (err) {
                console.log("Error", err);
            } else {
                console.log("Successfully added message", data.MessageId);
            }
        });
    }
    
    return res.status(200).json({
        message: "Message send successfully.",
        body: payload ? payload : [],
    });

}