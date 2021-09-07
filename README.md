# file-uploader-tech-test

### Features
- OpenAPI v3 Compliant API
- API Documentation and Playground
- Ability to upload files and store on local server (In future filesystem should be abstracted away
)
- Persistence using Postgres Database
- Ability to list all available files

### Running the project

- Install the Symfony CLI binary as documented here https://symfony.com/download
- Make sure to run PHP 8.0 and that Composer is installed
- Docker is required for the Postgres Database
- Install the project using `composer install`
- Start the local development server using ``symfony serve``
- Bring the database up using `docker-compose up -d`


 `http://localhost:8000` - Base URL of the project

`http://localhost:8000/api/doc` - API Documentation and API Sandbox environment 

---
###Project Requirements
*Following is taken from provided PDF for the tech test*

Using Laravel, write a RESTful API that allows an anonymous user to:

1. Upload a file 
2. The maximum file size should be checked and the file should be rejected if it exceeds 1MB
3. For each successfully uploaded file, store the file, and register a persistent download URL that enables the client side to retrieve that
unique file 
4. Return the list of persistent URLs using JSON via another RESTful API 
5. Write a simple single page front-end that allows a user to upload a file and download an individual persistent file from the list of files 
6. Include a script of curl calls that test all of the APIs you implement

Your approach
1. You can choose to use Laravel or not. Either way, you must include full instructions to enable us to locally deploy the application in a
working state
2. Please create a Github private repository, upload your solution to it and share your GitHub with us

