<?php

namespace App\Domain\Models;

use PDO;

class KeyboardsModel extends BaseModel
{

    /**
     * Returns the list of available Keyboards and Mice
     * @return array
     */
    public function getKeyboards(array $filters): array
    {
        $placeholder_values = [];
        $sql = "SELECT * FROM keyboards WHERE 1=1";
        //* Apply the vendor name filter
        $keyboard_name = $filters["name"] ?? '';

        if (!empty($keyboard_name)) {
            $sql .= " AND name LIKE CONCAT('%', :keyboard_name, '%') ";
            $placeholder_values["keyboard_name"] = $keyboard_name;
        }

        //if (!empty($vendor_country)) {
        // $sql .= " AND country = :vendor_country ";
        // $placeholder_values["vendor_country"] = $vendor_country;
        //}

        //todo append the limit

        return $this->paginate($sql, $placeholder_values);
    }

    public function getKeyboardsById(int $keyboard_id): array | false
    {
        //$sql = "SELECT * FROM vendors WHERE vendor_id  = {$vendor_id}";

        //Prepared statement:
        $sql = "SELECT * FROM keyboards WHERE keyboard_id = :keyboard_id ";
        return $this->fetchSingle(
            $sql,
            //Fill in the blank(s): Bind the placeholder name(s) to their values
            ["keyboard_id" => $keyboard_id]
        );
    }
    public function getReviewsByKeyboardID(string $keyboard_id): mixed
    {
        // 1) Fetch the details of the player parent resource.
        $keyboard = $this->getKeyboardsById($keyboard_id);

        //NOTE: The content of the PHP heredoc must be properly indented.
        // @see: https://www.php.net/manual/en/language.types.string.php#language.types.string.syntax.heredoc
        $reviews_query = <<<SQL
        SELECT * FROM reviews g
        WHERE keyboard_id = :keyboard_id
    SQL;

        // 2) Fetch the items of the goals sub-collection along with the tournament and match info.
        $switches = $this->paginate(
            $reviews_query,
            ["keyboard_id" => $keyboard_id]
        );
        // 3) Produce a well structured response for the goals sub-collection.
        $results = [
            "keyboard" => $keyboard,
            "reviews" => [
                "meta" => $switches["meta"],
                "data" => $switches["data"],
            ]
        ];

        return $results;
    }
}
