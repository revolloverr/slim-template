<?php

namespace App\Controllers;

use App\Domain\Models\KeyboardsModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class KeyboardsController extends BaseController
{
    //private VendorsModel$ vendors_model;
    //DI; Dependency Injection
    public function __construct(private KeyboardsModel $keyboards_model) {}



    // General Methods: e.g.. inputs, validation, formatting.

    // Callback methods: used for handling HTTP requests.
    //* GET /vendors
    public function handleGetKeyboards(Request $request, Response $response): Response
    {
        //echo "Quack!";exit;
        $filters = $request->getQueryParams();
        //please validate the pagination controls
        //!both positive integers
        $this->keyboards_model->setPaginationOptions($filters["page"], $filters["page_size"]);
        //* Fetch the list of vendors from the DB
        $keyboards = $this->keyboards_model->getKeyboards($filters);
        //* 2) Prepare a JSON response.
        //* 2.a) converts into JSON message
        //$data = json_encode($vendors);
        //* 2.b) Write the
        //$response->getBody()->write($data);

        //return $response->withHeader(
        //    "Content-Type",
        //    "application/json;charset=UTF-8"
        //);
        return $this->renderJson($response, $keyboards);
    }

    // Callback methods: used for handling HTTP requests.
    //* GET /vendors/{vendor_id}

    //singleton vendor resource
    public function handleGetKeyboardsById(Request $request, Response $response, array $args): Response
    {
        //* 1) Retrieve the vendor ID from the URIs dynamic segment.
        $keyboard_id = $args["keyboard_id"];

        //! Alternative Path: edge case
        //request header fucks you over/inputs
        //contextual: retireve by ID (nonexistant id/record/row) in the db=> 404 json error
        //403 forbidden request if create does the user have privilege

        //? Invalid vendor id: positive integer
        // if (!is_int((int)$vendor_id) || $vendor_id <= 0) {
        //option 1: Using an HTTP Specialized Exception
        //throw new HttpBadRequestException(
        // $request,
        //"The vendor ID was no bueno, it must be a positive int"
        //);

        //option 2:
        //}

        $pattern = "/^\d{1,9}$/";
        if (preg_match($pattern, $keyboard_id) === 0) {
            //*invalid id detected we still need to return a valid http error response
            //you can use the http specialized exception or throw one yourself
            //option 2 you prepare and return the http error response yourself
            //throw new HttpBadRequestException(
            // $request, "The received id aint valid lil bro"
            //);

            $payload = [
                "status" => "error",
                "code" => "400",
                "Message" => "The keyboard id aint right must be pos int no strings fr"
            ];

            return $this->renderJson($response, $payload, 400);
            //$response->getBody()->write(json_encode($payload));
            //return $response->withHeader("Content-Types", "application/json;charset=UTF-8")->withStatus(400);
        }
        //* 2) Pass the vendor id to the model to fetch any potential matching vendor
        $keyboard = $this->keyboards_model->getKeyboardsById($keyboard_id);

        //! what if the vendor was false or not found
        //! prep und return valid http 404 response for that
        if (
            $keyboard === false
        ) {
            //throw new HttpNotFoundException(
            //    $request,
            //    "There was no matching record for vendor id: $vendor_id"
            //);
            $payload = [
                "status" => "error",
                "code" => "404",
                "Message" => "There was no matching record for keyboard id: $keyboard_id"
            ];
            return $this->renderJson($response, $payload, 404);
        }
        //throw http exception
        return $this->renderJson($response, $keyboard);

        //* 3) Prepare a valid http response!
        //$data = json_encode($vendor);
        // dd($payload);
        //$response->getBody()->write($data);

        //return $response->withHeader(
        //    "Content-Type",
        //    "application/json;charset=UTF-8"
        //);
    }
    public function handleGetReviews(Request $request, Response $response, array $args): Response
    {
        //echo "Quack!";exit;
        //$filters = $request->getQueryParams();
        //please validate the pagination controls
        //!both positive integers
        //$this->vendors_model->setPaginationOptions($filters["page"], $filters["page_size"]);
        $keyboard_id = $args["keyboard_id"];
        //* Fetch the list of vendors from the DB
        $reviews = $this->keyboards_model->getReviewsByKeyboardID($keyboard_id);
        //* 2) Prepare a JSON response.
        //* 2.a) converts into JSON message
        //$data = json_encode($vendors);
        //* 2.b) Write the
        //$response->getBody()->write($data);

        //return $response->withHeader(
        //    "Content-Type",
        //    "application/json;charset=UTF-8"
        //);
        return $this->renderJson($response, $reviews);
    }
}
