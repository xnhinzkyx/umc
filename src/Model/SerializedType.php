<?php
declare(strict_types=1);

namespace App\Model;

use Twig\Environment;

class SerializedType
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var Serialized
     */
    private $serialized;
    /**
     * @var string
     */
    private $label;
    /**
     * @var bool
     */
    private $canHaveOptions;
    /**
     * @var bool
     */
    private $canBeRequired;
    /**
     * @var bool
     */
    private $multiple;
    /**
     * @var string|null
     */
    private $sourceModel;
    /**
     * @var array
     */
    private $templates;

    /**
     * SerializedType constructor.
     * @param Environment $twig
     */
    public function __construct(Environment $twig, Serialized $serialized, array $data)
    {
        $this->twig = $twig;
        $this->serialized = $serialized;
        $this->label = (string)($data['label'] ?? null);
        $this->canHaveOptions = (bool)($data['can_have_options'] ?? false);
        $this->canBeRequired = (bool)($data['can_be_required'] ?? false);
        $this->multiple = (bool)($data['multiple'] ?? false);
        $this->sourceModel = $data['source_model'] ?? null;
        $this->templates = isset($data['templates']) && is_array($data['templates']) ? $data['templates'] : [];
    }

    /**
     * @return string
     */
    public function getMultipleText(): string
    {
        return $this->multiple ? 'true' : 'false';
    }

    /**
     * @return bool
     */
    public function isCanHaveOptions(): bool
    {
        return $this->canHaveOptions;
    }

    /**
     * @return string
     */
    public function getSourceModel(): string
    {
        return $this->sourceModel ?? $this->serialized->getOptionSourceVirtualType();
    }

    /**
     * @return Serialized
     */
    public function getSerialized(): Serialized
    {
        return $this->serialized;
    }

    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderForm(): string
    {
        return !empty($this->templates['serialized']['backend'])
            ? $this->renderTemplate($this->templates['serialized']['backend'])
            : '';
    }

    /**
     * @param null|string $template
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private function renderTemplate(?string $template): string
    {
        if (!$template) {
            return '';
        }
        return $this->twig->render(
            $template,
            [
                'type' => $this,
                'field' => $this->serialized,
                'attribute' => $this->serialized->getAttribute(),
                'entity' => $this->serialized->getAttribute()->getEntity(),
                'module' => $this->serialized->getAttribute()->getEntity()->getModule(),
                'indent' => $this->serialized->isExpanded() ? '' : str_repeat(' ', 4)
            ]
        );
    }
}
