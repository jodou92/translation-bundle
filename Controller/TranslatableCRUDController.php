<?php

namespace Umanit\TranslationBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @author Arthur Guigand <aguigand@umanit.fr>
 */
class TranslatableCRUDController extends CRUDController
{
    /**
     * Translate an entity
     *
     * @return RedirectResponse
     */
    public function translateAction()
    {
        $request = $this->getRequest();

        $id     = $request->get($this->admin->getIdParameter());
        $locale = $request->get('newLocale');
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $newObject = $this->admin->getModelManager()->findOneBy(get_class($object), ['oid' => $object->getOid(), 'locale' => $locale]);

        if (empty($newObject)) {
            $this->admin->checkAccess('edit', $object);

            $newObject = $this->get('umanit_translation.translator.entity_translator')->getEntityTranslation($object, $locale);

            $this->addFlash('sonata_flash_success', 'Translated successfully!');
        }

        return new RedirectResponse($this->admin->generateUrl('edit', ['id' => $newObject->getId()]));
    }
}