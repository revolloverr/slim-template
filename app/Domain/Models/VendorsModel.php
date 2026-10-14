<?php

namespace App\Domain\Models;

use PDO;

class VendorsModel extends BaseModel
{

    /**
     * Returns the list of available Keyboards and Mice
     * @return array
     */
    public function getVendors(array $filters): array
    {
        $placeholder_values = [];
        $sql = "SELECT * FROM vendors WHERE 1=1";
        //* Apply the vendor name filter
        $vendor_name = $filters["name"] ?? '';

        if (!empty($vendor_name)) {
            $sql .= " AND name LIKE CONCAT('%', :vendor_name, '%') ";
            $placeholder_values["vendor_name"] = $vendor_name;
        }

        $vendor_country = $filters["country"] ?? '';

        if (!empty($vendor_country)) {
            $sql .= " AND country = :vendor_country ";
            $placeholder_values["vendor_country"] = $vendor_country;
        }

        //todo append the limit

        return $this->paginate($sql, $placeholder_values);
    }

    public function getVendorsById(int $vendor_id): array | false
    {
        //$sql = "SELECT * FROM vendors WHERE vendor_id  = {$vendor_id}";

        //Prepared statement:
        $sql = "SELECT * FROM vendors WHERE vendor_id = :vendor_id ";
        return $this->fetchSingle(
            $sql,
            //Fill in the blank(s): Bind the placeholder name(s) to their values
            ["vendor_id" => $vendor_id]
        );
    }
    public function getSwitchesByVendorID(string $vendor_id): mixed
    {
        // 1) Fetch the details of the player parent resource.
        $vendor = $this->getVendorsById($vendor_id);

        //NOTE: The content of the PHP heredoc must be properly indented.
        // @see: https://www.php.net/manual/en/language.types.string.php#language.types.string.syntax.heredoc
        $switches_query = <<<SQL
        SELECT * FROM switches g
        WHERE vendor_id = :vendor_id
    SQL;

        // 2) Fetch the items of the goals sub-collection along with the tournament and match info.
        $switches = $this->paginate(
            $switches_query,
            ["vendor_id" => $vendor_id]
        );
        // 3) Produce a well structured response for the goals sub-collection.
        $results = [
            "vendor" => $vendor,
            "switches" => [
                "meta" => $switches["meta"],
                "data" => $switches["data"],
            ]
        ];

        return $results;
    }
}
