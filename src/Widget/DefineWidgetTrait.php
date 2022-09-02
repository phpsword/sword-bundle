<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Widget;

trait DefineWidgetTrait
{
    private function defineWidget(
        string $id,
        string $name,
        string $description,
        string $cssClass,
        array $fields,
    ): void {
        $this->widget_id = $id;
        $this->widget_name = $name;
        $this->widget_description = $description;
        $this->widget_cssclass = $cssClass;
        $this->settings = $fields;
    }
}
