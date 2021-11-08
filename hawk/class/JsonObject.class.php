<?php



abstract class JsonObject
{

    public final function __construct()
    {
    }

    public static function fromJson($jsonObj,bool $isStrict = TRUE)
    {
        //TODO DOCUMENTATION
        $obj = new static();
        $attributs = array_keys(get_object_vars($obj));
        var_dump($attributs);
        foreach ($attributs as $attribut) {
            var_dump($jsonObj);
            if (!property_exists($jsonObj, $attribut)) {
                return FALSE;
            }
            $obj->$attribut = $jsonObj->$attribut;
        }
        return $obj;
    }

}
