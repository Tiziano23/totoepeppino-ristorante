<?php

abstract class BaseModel implements JsonSerializable
{
    protected $id;
    protected $tableName;
    protected $properties = array();
    protected $isSynced;

    public function __construct($id, $tableName)
    {
        $this->id = $id;
        $this->tableName = $tableName;
        $this->load();
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function setProperty($key, $value)
    {
        $this->properties[$key] = $value;
    }
    public function getProperty($key)
    {
        return $this->properties[$key];
    }
    public function delete(): bool
    {
        $this->isSynced = $this->deleteQuery();
        return $this->isSynced;
    }
    public function sync(): bool
    {
        $this->save();
        return $this->isSynced;
    }

    abstract protected function load();
    protected function save()
    {
        if ($this->selectQuery()->num_rows > 0)
            $this->isSynced = $this->updateQuery();
        else
            $this->isSynced = $this->insertQuery();
    }

    protected function selectQuery()
    {
        global $db;
        $id = $db->real_escape_string($this->id);
        $table = $db->real_escape_string($this->tableName);
        return $db->query(sprintf("SELECT * FROM `%s` WHERE `id` = %d", $table, $id));
    }
    protected function deleteQuery()
    {
        global $db;
        $id = $db->real_escape_string($this->id);
        $table = $db->real_escape_string($this->tableName);
        return $db->query(sprintf("DELETE FROM `%s` WHERE `id` = %d", $table, $id));
    }
    protected abstract function insertQuery();
    protected abstract function updateQuery();

    public abstract static function fetchId($id);

    public function jsonSerialize(): mixed
    {
        return [
            "id" => $this->id,
            ...$this->properties
        ];
    }
}

// ====================================================

class MenuCategory extends BaseModel
{
    public const TABLE_NAME = "categories";

    public function __construct($id)
    {
        parent::__construct($id, MenuCategory::TABLE_NAME);
    }
    protected function load()
    {
        $res = $this->selectQuery();
        if ($res) {
            $data = $res->fetch_assoc();
            $this->properties = $data;
        }
        $this->isSynced = $res ? true : false;
    }
    protected function insertQuery()
    {
        global $db;
        $name = $db->real_escape_string($this->properties['name']);
        $color = $db->real_escape_string($this->properties['color']);
        $order = $db->real_escape_string($this->properties['order']);
        return $db->query(sprintf("INSERT INTO `%s` (`name`, `color`, `order`) VALUES ('%s', '%s', %d)", $this->tableName, $name, $color, $order));
    }
    protected function updateQuery()
    {
        global $db;
        $id = $db->real_escape_string($this->id);
        $name = $db->real_escape_string($this->properties['name']);
        $color = $db->real_escape_string($this->properties['color']);
        $order = $db->real_escape_string($this->properties['order']);
        return $db->query(sprintf("UPDATE `%s` SET `name`='%s', `color`='%s', `order`=%d WHERE `id`=%d", $this->tableName, $name, $color, $order, $id));
    }

    public static function create($name, $color = 'DDC091', $order = 0): MenuCategory | null
    {
        global $db;
        $name = $db->real_escape_string($name);
        $color = $db->real_escape_string($color);
        $order = $db->real_escape_string($order);
        $res = $db->query(sprintf("INSERT INTO `%s` (`name`, `color`, `order`) VALUES ('%s', '%s', %d)", MenuCategory::TABLE_NAME, $name, $color, $order));
        return $res ? new MenuCategory($db->insert_id) : null;
    }
    public static function fetchId($id): MenuCategory | null
    {
        global $db;
        $id = $db->real_escape_string($id);
        $res = $db->query(sprintf("SELECT * FROM `%s` WHERE `id`=%d", MenuCategory::TABLE_NAME, $id));
        return $res ? new MenuCategory($res->fetch_assoc()['id']) : null;
    }
    /**
     * @return MenuCategory[]
     */
    public static function fetchAll(): array
    {
        global $db;
        $result = $db->query(sprintf("SELECT * FROM `%s` ORDER BY `order`", MenuCategory::TABLE_NAME));
        $entries = [];
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($data as $entry)
                array_push($entries, new MenuCategory($entry['id']));
        }
        return $entries;
    }
}

class MenuSubCategory extends BaseModel
{
    public const TABLE_NAME = "subcategories";

    public function __construct($id)
    {
        parent::__construct($id, MenuSubCategory::TABLE_NAME);
    }
    protected function load()
    {
        $res = $this->selectQuery();
        if ($res) {
            $data = $res->fetch_assoc();
            $this->properties['name'] = $data['name'];
            $this->properties['category_id'] = $data['category_id'];

            $this->properties['order'] = $data['order'];
        }
        $this->isSynced = $res ? true : false;
    }
    protected function insertQuery()
    {
        global $db;
        $name = $db->real_escape_string($this->properties['name']);
        $category_id = $db->real_escape_string($this->properties['category_id']);

        $order = $db->real_escape_string($this->properties['order']);
        return $db->query(sprintf("INSERT INTO `%s` (`name`, `category_id`) VALUES ('%s', %d)", $this->tableName, $name, $category_id));
    }
    protected function updateQuery()
    {
        global $db;
        $id = $db->real_escape_string($this->id);
        $name = $db->real_escape_string($this->properties['name']);
        $order = $db->real_escape_string($this->properties['order']);
        $category_id = $db->real_escape_string($this->properties['category_id']);
        return $db->query(sprintf("UPDATE `%s` SET `name`='%s', `order`=%d, `category_id`=%d WHERE `id`=%d", $this->tableName, $name, $order, $category_id, $id));
    }

    public static function create($name, $category_id, $order = 0): MenuSubCategory | null
    {
        global $db;
        $name = $db->real_escape_string($name);
        $order = $db->real_escape_string($order);
        $category_id = $db->real_escape_string($category_id);

        // $fields = "`title`, `descr`, `price`, `category_id`";
        // $valueTypes = "'%s', '%s', %f, %d";

        $res = $db->query(sprintf("INSERT INTO `%s` (`name`, `category_id`) VALUES ('%s', %d)", MenuSubCategory::TABLE_NAME, $name, $category_id));
        return $res ? new MenuSubCategory($db->insert_id) : null;
    }
    public static function fetchId($id): MenuSubCategory | null
    {
        global $db;
        $id = $db->real_escape_string($id);
        $res = $db->query(sprintf("SELECT * FROM `%s` WHERE `id`=%d", MenuSubCategory::TABLE_NAME, $id));
        return $res && $res->num_rows > 0 ? new MenuSubCategory($res->fetch_assoc()['id']) : null;
    }
    /**
     * @return MenuSubCategory[]
     */
    public static function fetchAll(): array
    {
        global $db;
        $result = $db->query(sprintf("SELECT * FROM `%s`", MenuSubCategory::TABLE_NAME));
        $entries = [];
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($data as $entry)
                array_push($entries, new MenuSubCategory($entry['id']));
        }
        return $entries;
    }
    /**
     * @return MenuSubCategory[]
     */
    public static function fetchByCategory($category): array
    {
        global $db;

        $result = $db->query(sprintf("SELECT * FROM `%s` WHERE `category_id`=%d ORDER BY `order`", MenuSubCategory::TABLE_NAME, $category->getId()));
        $entries = [];
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($data as $entry)
                array_push($entries, new MenuSubCategory($entry['id']));
        }
        return $entries;
    }
}

class MenuEntry extends BaseModel
{
    public const TABLE_NAME = "entries";

    public function __construct($id)
    {
        parent::__construct($id, MenuEntry::TABLE_NAME);
    }
    protected function load()
    {
        $res = $this->selectQuery();
        if ($res) {
            $data = $res->fetch_assoc();
            $this->properties['title'] = $data['title'];
            $this->properties['descr'] = $data['descr'];
            $this->properties['price'] = $data['price'];
            $this->properties['category_id'] = $data['category_id'];

            $this->properties['order'] = $data['order'];
            $this->properties['subcategory_id'] = $data['subcategory_id'];
        }
        $this->isSynced = $res ? true : false;
    }
    protected function insertQuery()
    {
        global $db;
        $title = $db->real_escape_string($this->properties['title']);
        $descr = $db->real_escape_string($this->properties['descr']);
        $price = $db->real_escape_string($this->properties['price']);
        $category_id = $db->real_escape_string($this->properties['category_id']);

        $order = $db->real_escape_string($this->properties['order']);
        $subcategory_id = $db->real_escape_string($this->properties['subcategory_id']);

        return $db->query(sprintf("INSERT INTO `%s` (`title`, `descr`, `price`, `category_id`, `order`, `subcategory_id`) VALUES ('%s', '%s', '%s', %d, %d, %d)", $this->tableName, $title, $descr, $price, $category_id, $order, $subcategory_id));
    }
    protected function updateQuery()
    {
        global $db;
        $id = $db->real_escape_string($this->id);
        $title = $db->real_escape_string($this->properties['title']);
        $descr = $db->real_escape_string($this->properties['descr']);
        $price = $db->real_escape_string($this->properties['price']);
        $category_id = $db->real_escape_string($this->properties['category_id']);
        $order = isset($this->properties['order']) ? $db->real_escape_string($this->properties['order']) : null;
        $subcategory_id = isset($this->properties['subcategory_id']) ? $db->real_escape_string($this->properties['subcategory_id']) : null;

        $updateString = "`title`='%s', `descr`='%s', `price`='%s', `category_id`=%d";
        $values = [$this->tableName, $title, $descr, $price, $category_id];

        if (isset($order)) {
            $updateString .= ", `order`=%d";
            array_push($values, $order);
        }
        if (isset($subcategory_id)) {
            if ($subcategory_id != -1) {
                $updateString .= ", `subcategory_id`=%d";
                array_push($values, $subcategory_id);
            } else {
                $updateString .= ", `subcategory_id`=%s";
                array_push($values, "NULL");
            }
        }
        array_push($values, $id);

        return $db->query(vsprintf("UPDATE `%s` SET $updateString WHERE id=%d", $values));
    }

    public static function create($title, $descr, $price, $category_id, $subcategory_id = null, $order = null): MenuEntry | null
    {
        global $db;
        $title = $db->real_escape_string($title);
        $descr = $db->real_escape_string($descr);
        $price = $db->real_escape_string($price);
        $category_id = $db->real_escape_string($category_id);
        $order = isset($order) ? $db->real_escape_string($order) : null;
        $subcategory_id = isset($subcategory_id) ? $db->real_escape_string($subcategory_id) : null;

        $fields = "`title`, `descr`, `price`, `category_id`";
        $valueTypes = "'%s', '%s', '%s', %d";
        $values = [MenuEntry::TABLE_NAME, $title, $descr, $price, $category_id];

        if (isset($order)) {
            $fields .= ", `order`";
            $valueTypes .= ", %d";
            array_push($values, $order);
        }
        if (isset($subcategory_id)) {
            $fields .= ", `subcategory_id`";
            $valueTypes .= ", %d";
            array_push($values, $subcategory_id);
        }

        $res = $db->query(vsprintf("INSERT INTO `%s` ($fields) VALUES ($valueTypes)", $values));

        return $res ? new MenuEntry($db->insert_id) : null;
    }
    public static function fetchId($id): MenuEntry | null
    {
        global $db;
        $id = $db->real_escape_string($id);
        $res = $db->query(sprintf("SELECT * FROM `%s` WHERE `id`=%d", MenuEntry::TABLE_NAME, $id));
        return $res ? new MenuEntry($res->fetch_assoc()['id']) : null;
    }
    /**
     * @return MenuItem[]
     */
    public static function fetchAll(): array
    {
        global $db;
        $result = $db->query(sprintf("SELECT * FROM `%s` ORDER BY `category_id`, `order`", MenuEntry::TABLE_NAME));
        $entries = [];
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($data as $entry)
                array_push($entries, new MenuEntry($entry['id']));
        }
        return $entries;
    }
    /**
     * @return MenuItem[]
     */
    public static function fetchByCategory($category): array
    {
        global $db;

        // 
        $result = $db->query(sprintf("SELECT * FROM `%s` WHERE `category_id`=%d AND `subcategory_id` IS NULL ORDER BY `order`", MenuEntry::TABLE_NAME, $category->getId()));
        $entries = [];
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($data as $entry)
                array_push($entries, new MenuEntry($entry['id']));
        }
        return $entries;
    }
    /**
     * @return MenuItem[]
     */
    public static function fetchBySubCategory($subcaegory): array
    {
        global $db;

        $result = $db->query(sprintf("SELECT * FROM `%s` WHERE `category_id`=%d AND `subcategory_id`=%d ORDER BY `order`", MenuEntry::TABLE_NAME, $subcaegory->getProperty("category_id"), $subcaegory->getId()));
        $entries = [];
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($data as $entry)
                array_push($entries, new MenuEntry($entry['id']));
        }
        return $entries;
    }
}
