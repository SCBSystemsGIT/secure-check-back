<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\Response;
#use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Helpers
{
    public function formatDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }

    public function validateUserData(array $data): array
    {
        $errors = [];

        // Add your validation logic here
        if (empty($data['nom'])) {
            $errors[] = 'Nom is required';
        }
        if (empty($data['email'])) {
            $errors[] = 'Email is required';
        }

        return $errors;
    }

    function validateRequiredFields(array $data, array $requiredFields): array
    {
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        return $missingFields;
    }

    /**
     * Validate required fields in an entity.
     *
     * @param object $entity
     * @param array $requiredFields
     * @return array
     */
    public function validateRequiredFieldsentity(object $entity, array $requiredFields): array
    {
        $missingFields = [];

        foreach ($requiredFields as $field) {
            $getter = 'get' . ucfirst($field);

            if (method_exists($entity, $getter)) {
                if (empty($entity->$getter())) {
                    $missingFields[] = $field;
                }
            } else {
                $missingFields[] = $field;
            }
        }

        return $missingFields;
    }

    function returnResponse(string $message, int $status = Response::HTTP_OK, $data = [], $errors = []): array
    {
        return [
            "message" => $message == "" ? Response::$statusTexts['200'] : $message,
            "status_code" => $status,
            "data" => $data,
            "errors" => $errors
        ];
    }

    function returnErrorResponse(string $message, int $status = Response::HTTP_BAD_REQUEST, $data = [], $errors = []): array
    {
        return [
            "message" => $message == "" ? Response::$statusTexts['400'] : $message,
            "status_code" => $status,
            "data" => $data,
            "errors" => $errors
        ];
    }

    function array_except($data, $key)
    {
        unset($data["$key"]);

        return $data;
    }


    /**
     * Summary of checkNotEmpty
     * @param mixed $elements
     * @param mixed $required_values
     * @return array
     */
    function checkNotEmpty($elements = [], $required_values = [])
    {
        $all = [];

        if (count($required_values) >= 1) {

            foreach ($required_values as $value) {

                if (isset($elements[$value])) {
                    if ($elements[$value] === "" || is_null($elements[$value])) {
                        $error = ['message' => sprintf('Champ requis non renseigné : %s ', $value)];
                        array_push($all, $error);
                    }
                } else {
                    $error = ['message' => sprintf('Champ réquis manquant dans le payload  : %s ', $value)];
                    array_push($all, $error);
                }
            }
        } else {

            foreach ($elements as $key => $element) {

                if ($element === "" || is_null($element)) {
                    $error = ['message' => sprintf('Champ non renseigné : %s ', $key)];
                    array_push($all, $error);
                }
            }
        }

        if (count($all) > 0) {
            return returnResponse("Paramètre requis manquant", Response::HTTP_UNPROCESSABLE_ENTITY, errors: array_merge($all));
        }
    }

    function isDigits(string $s, int $minDigits = 9, int $maxDigits = 14): bool
    {
        return preg_match('/^[0-9]{' . $minDigits . ',' . $maxDigits . '}\z/', $s);
    }

    function isValidTelephoneNumber(string $telephone, int $minDigits = 9, int $maxDigits = 14): bool
    {
        if (preg_match('/^[+][0-9]/', $telephone)) { //is the first character + followed by a digit
            $count = 1;
            $telephone = str_replace(['+'], '', $telephone, $count); //remove +
        }

        //remove white space, dots, hyphens and brackets
        $telephone = str_replace([' ', '.', '-', '(', ')'], '', $telephone);

        //are we left with digits only?
        return isDigits($telephone, $minDigits, $maxDigits);
    }

}