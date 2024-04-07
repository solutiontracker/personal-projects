const serverless = require("serverless-http");
const express = require("express");
const app = express();
const expressListRoutes = require('express-list-routes');
const sparkpostRoute = require('./routes/sparkpost')

app.use(express.json({ limit: '100mb', extended: true }));

app.use('/api', sparkpostRoute)

app.use((req, res, next) => {
  return res.status(404).json({
    error: "Not Found",
  });
});

app.use((error, req, res, next) => {

  console.log("Error Handling Middleware called")
  console.log('Path: ', req.path)
  console.error('Error: ', error)

  if (error.type == 'redirect')
    res.redirect('/error')
  else if (error.type == 'time-out') // arbitrary condition check
    res.status(408).send(error)
  else
    res.status(500).send({ ...error, queue: `https://sqs.eu-west-1.amazonaws.com/${process.env.AWS_ACCOUNT_ID}/${process.env.AWS_REQUEST_TO_SPEAK_QUEUE}` })

});

expressListRoutes(sparkpostRoute);

module.exports.handler = serverless(app);
