<?php

Class Xby2BaseView {

    public $meta;
    public $inputs = [];

    public function __construct($meta = null)
    {
        if ($meta){
            $this->meta = $meta;
        }
    }

    /**
     * Add an input to the array of inputs that will generate the Meta Box
     *
     * @param $type
     * @param $id
     * @param $label
     * @param $name
     * @param $class
     * @param array $dropdownArray
     * @param null $inputDescription
     */
    public function addInput($type, $id, $label, $name, $class, $dropdownArray = [], $inputDescription = null) {
        $this->inputs[] = array(
            "type"  => $type,
            "id"    => $id,
            "label" => $label,
            "name"  => $name,
            "class" => $class,
            "dropdownArray" => $dropdownArray,
            "inputDescription" => $inputDescription
        );
    }

    /**
     * Display the form table with all the inputs
     */
    public function displayForm($post_type = null)
    {
        ?>
        <table class="form-table">
            <tbody>
                <?php $this->renderInputs(); ?>
            </tbody>
        </table>

        <?php
        // Check if there is any additional HTML content based on the post_type
        if (!is_null($post_type)) {
            if ($post_type == 'service') { ?>
                    <div align="right">
                    	<button id="addPointButton" type="button" class="button button-primary">Add Point</button>
                    	<button id="minusPointButton" type="button" class="button">Remove Point</button>
                    </div>
                    <p class="description service-description" align="right">Please enter a maximum of 6 points</p>
            <?php
            }
        }
    }

    /**
     * Render all the inputs that have been added to the $inputs array
     */
    public function renderInputs()
    {
        foreach ($this->inputs as $key => $value) {
            // Call the class function corresponding to the desired input type
            if ( isset($value["type"]) ) {
                $type = $value["type"];
                if ( method_exists($this, $type) ){
                    $this->$type($value);
                }
            }
        }
    }

    /**
     * Standard text input
     * @param $data
     */
    public function text($data)
    {
        extract($data);
        ?>
        <tr class="<?=$id?>-row">
            <th scope="row">
                <label for="<?=$id?>"><?=$label?></label>
            </th>
            <td>
                <input id="<?=$id?>" class="<?=$class?>" name="<?=$name?>"
                    <?php if(isset($this->meta[$name])) { echo 'value="'.$this->meta[$name][0].'"'; } ?>
                >
                <?php if (!empty($inputDescription)): ?>
                    <p id="<?=$id?>-desc" class="description remaining-characters">Please enter a maximum of 180 characters. Remaining: <span></span></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }

    /**
     * Text input with a button to open the Media viewer to select an image
     * @param $data
     */
    public function imageSelect($data)
    {
        extract($data);
        ?>
        <tr class="<?=$id?>-row">
            <th scope="row">
                <label for="<?=$id?>"><?=$label?></label>
            </th>
            <td>
                <input id="<?=$id?>" class="<?=$class?>" name="<?=$name?>"
                    <?php if(isset($this->meta[$name])) { echo 'value="'.$this->meta[$name][0].'"'; } ?>
                >
                <button id="<?=$id?>-media-button" type="button" class="button button-default select-media-button">Select Image</button>
            </td>
        </tr>
        <?php
    }

    /**
     * Checkbox input
     * @param $data
     */
    public function checkbox($data)
    {
        extract($data);
        ?>
        <tr class="<?=$id?>-row">
            <th scope="row">
                <label for="<?=$id?>"><?=$label?></label>
            </th>
            <td>
                <input type="checkbox" id="<?=$id?>" name="<?=$name?>"
                    <?php if(isset($this->meta[$name]) && $this->meta[$name][0] == true) { echo 'checked'; } ?>
                >
            </td>
        </tr>
        <?php
    }

    /**
     * Group of checkbox inputs (used for selecting multiple services for a Client Story)
     * @param $data
     */
    public function checkboxList($data)
    {
        extract($data);
        $expertises = isset($this->meta[$name]) ? unserialize($this->meta[$name][0]) : [];
        ?>
        <tr class="<?=$id?>-row">
            <th scope="row">
                <label for="<?=$id?>"><?=$label?></label>
            </th>
            <td>
                <?php foreach($dropdownArray as $key=>$post): ?>
                    <label>
                        <input type="checkbox" name="<?=$name?>[]" value="<?=$post->ID?>"
                            <?php if(in_array($post->ID, $expertises)) { echo 'checked'; } ?>
                        />
                        <?=$post->post_title?>
                    </label>
                    &nbsp;&nbsp;&nbsp;
                <?php endforeach; ?>
            </td>
        </tr>
        <?php
    }

    /**
     * Dropdown input
     * @param $data
     */
    public function dropdown($data)
    {
        extract($data);
        ?>
        <tr class="<?=$id?>-row">
            <th scope="row">
                <label for="<?=$id?>"><?=$label?></label>
            </th>
            <td>
                <select id="<?=$id?>" name="<?=$name?>" class="<?=$class?>">
                    <?php foreach($dropdownArray as $key=>$post): ?>
                        <option value="<?php echo $post->ID ?>"
                            <?php if (isset($this->meta[$name][0]) && (int)$this->meta[$name][0] == $post->ID) {
                                echo 'selected';
                            } ?>
                        >
                            <?php echo ucfirst($post->post_title)?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <?php
    }

    /**
     * List of text inputs (used for list of points for Service)
     * @param $data
     */
    public function textList($data)
    {
        extract($data);

        // If the dropdown array is empty, display only one empty input
        if (empty($dropdownArray)) { ?>
            <tr class="<?=$id?>-row">
                <th scope="row">
                    <label for="<?=$id?>"><?=$label?></label>
                </th>
                <td>
                    <input id="<?=$id?>" class="<?=$class?>" name="<?=$name?>[]">
                </td>
            </tr>
            <?php
        } else {
            foreach ($dropdownArray as $key=>$value) : ?>
                <tr class="<?=$id?>-row">
                    <th scope="row">
                        <label for="<?=$id?>"><?php echo ($key == 0) ? $label : '';  ?></label>
                    </th>
                    <td>
                        <input id="<?=$id?>" class="<?=$class?>" name="<?=$name?>[]"
                            <?php if(isset($this->meta[$name])) {
                                $val = (!empty($value)) ? $value : '';
                                echo 'value="'.$val.'"';
                            } ?>>
                    </td>

                </tr>
            <?php endforeach;
        }
    }
}