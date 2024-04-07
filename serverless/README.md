### Deployment

Install dependencies with:

npm install

npm install -g serverless

If you don’t already have Node.js on your machine, install it first. If you don't want to install Node or NPM, you can install serverless as a standalone binary.

Creating A Service

# Create a new serverless project
  serverless

  ? What do you want to make? (Use arrow keys)
  > AWS - Node.js - Starter
    AWS - Node.js - HTTP API
    AWS - Node.js - Scheduled Task
    AWS - Node.js - SQS Worker
    AWS - Node.js - Express API
    AWS - Node.js - Express API with DynamoDB
    AWS - Python - Starter
    AWS - Python - HTTP API
    AWS - Python - Scheduled Task
    AWS - Python - SQS Worker
    AWS - Python - Flask API
    AWS - Python - Flask API with DynamoDB
    Other

    You can choose any platform and template to create new service/lambda function

# Move into the newly created directory
cd your-service-name

The serverless command will guide you to:

Create a new project

  Configure your AWS credentials
  Optionally set up a free Serverless Framework account with additional features.
  Your new serverless project will contain a serverless.yml file. This file features simple syntax for deploying infrastructure to AWS, such as AWS Lambda functions, infrastructure that triggers those functions with events, and additional infrastructure your AWS Lambda functions may need for various use-cases. You can learn more about this in the Core Concepts documentation.

The serverless command will give you a variety of templates to choose from. If those do not fit your needs, check out the project examples from Serverless Inc. and our community. You can install any example by passing a GitHub URL using the --template-url option

serverless --template-url=https://github.com/serverless/examples/tree/v3/...

Deploy with:
  serverless deploy

Deploy with enviroment
  serverless deploy --stage stage
  serverless deploy --stage prod --region eu-west-1

Developing On The Cloud
  Many Serverless Framework users choose to develop on the cloud, since it matches reality and emulating Lambda locally can be complex. To develop on the cloud quickly, without sacrificing speed, we recommend the following workflow...

  To deploy code changes quickly, skip the serverless deploy command which is much slower since it triggers a full AWS CloudFormation update. Instead, deploy code and configuration changes to individual AWS Lambda functions in seconds via the deploy function command, with -f [function name in serverless.yml] set to the function you want to deploy.

  sls deploy function -f my-api

  To invoke your AWS Lambda function on the cloud, you can find URLs for your functions w/ API endpoints in the serverless deploy output, or retrieve them via serverless info. If your functions do not have API endpoints, you can use the invoke command, like this:
    
  sls invoke -f hello
 
  # Invoke and display logs:
  serverless invoke -f hello --log

  Developing Locally

  Many Serverless Framework users rely on local emulation to develop more quickly. Please note, emulating AWS Lambda and other cloud services is never accurate and the process can be complex. We recommend the following workflow to develop locally...

  Use the invoke local command to invoke your function locally:

  sls invoke local -f my-api

  You can also pass data to this local invocation via a variety of ways. Here's one of them:

  serverless invoke local --function functionName --data '{"a":"bar"}'

  Serverless Framework also has a great plugin that allows you to run a server locally and emulate AWS API Gateway. This is the serverless-offline command.

Serverless Logs	diffrent commands

  serverless logs -f hello
  # Optionally tail the logs with --tail or -t
  serverless logs -f hello -t
  serverless logs -f hello --startTime 5h
  serverless logs -f hello --startTime 1469694264
  serverless logs -f hello --filter serverless

List existing deploys
  serverless deploy list

List deployed functions and their versions
  serverless deploy list functions

_Note_: In current form, after deployment, your API is public and can be invoked by anyone. For production deployments, you might want to configure an authorizer. For details on how to do that, refer to [`httpApi` event docs](https://www.serverless.com/framework/docs/providers/aws/events/http-api/).

### Invocation

After successful deployment, you can call the created application via HTTP:

```bash
curl https://xxxxxxx.execute-api.us-east-1.amazonaws.com/
```

Which should result in the following response:

```
{"message":"Hello from root!"}
```

Calling the `/hello` path with:

```bash
curl https://xxxxxxx.execute-api.us-east-1.amazonaws.com/hello
```

Should result in the following response:

```bash
{"message":"Hello from path!"}
```

If you try to invoke a path or method that does not have a configured handler, e.g. with:

```bash
curl https://xxxxxxx.execute-api.us-east-1.amazonaws.com/nonexistent
```

You should receive the following response:

```bash
{"error":"Not Found"}
```

### Local development

It is also possible to emulate API Gateway and Lambda locally by using `serverless-offline` plugin. In order to do that, execute the following command:

```bash
serverless plugin install -n serverless-offline
```

It will add the `serverless-offline` plugin to `devDependencies` in `package.json` file as well as will add it to `plugins` in `serverless.yml`.

After installation, you can start local emulation with:

```
serverless offline
```

To learn more about the capabilities of `serverless-offline`, please refer to its [GitHub repository](https://github.com/dherault/serverless-offline).


Aws serverless login detail

https://app.serverless.com/eventbuizz 
mms@eventbuizz.com 
*Eventbuizz@2022* 