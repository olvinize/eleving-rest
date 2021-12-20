<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;

class NewOrderType extends AbstractType
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', null, ['empty_data' => Order::STATUS_PROCESSING])
            ->add('productsTotal', null, ['empty_data' => '0'])
            ->add('deliveryTotal', null, ['empty_data' => '0'])
            ->add('grandTotal', null, ['empty_data' => '0'])
            ->add('deliveryAddress')
            ->add('products', CollectionType::class, [
                'entry_type' => ProductType::class,
                'constraints' => [
                    new Callback(function ($payload, $context) {
                        foreach ($payload as $product) {
                            if (!$product->getId()) {
                                $context->buildViolation('Order contains missing products, please select others.')
                                    ->atPath('products')
                                    ->addViolation();
                                break;
                            }
                        }
                    })
                ]
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            /** @var Order $order */
            $order = $event->getForm()->getData();
            if (isset($data['products'])) {
                $productIds = $data['products'] ?? [];
                foreach ($productIds as $id) {
                    $product = $this->productRepository->find($id);
                    $order->addProduct($product ?: new Product());
                }
                unset($data['products']);
            }

            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class
        ]);
    }
}
