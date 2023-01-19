<?php

namespace Guave\FormSaveBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\ContentModel;
use Contao\FormFieldModel;
use Guave\FormSaveBundle\Model\ContentModel as GuaveContentModel;

/**
 * @Hook("getContentElement")
 */
class GetContentElementListener
{
    public function __invoke(ContentModel $contentModel, string $buffer, $element): string
    {
        if (empty($contentModel->form)) {
            return $buffer;
        }

        $formNameElement = GuaveContentModel::findPublishedByPidAndType($contentModel->pid, 'formName');
        if ($formNameElement === null) {
            return $buffer;
        }

        $aliasField = FormFieldModel::findBy(['pid = ?', 'name = ?'], [$contentModel->form, 'alias']);
        if ($aliasField === null) {
            return $buffer;
        }

        $aliasField->value = $formNameElement->formName;

        return $element->generate();
    }
}
