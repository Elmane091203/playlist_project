<?php
namespace App\Form;

use App\Entity\Media;
use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;

class MediaType extends AbstractType
{
    private $security;
    private $playlistRepository;
    public function __construct(Security $security, PlaylistRepository $playlistRepository)
    {
        $this->security = $security;
        $this->playlistRepository = $playlistRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        $playlists = $this->playlistRepository->findBy(['user' => $user]);
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control']
            ])
            ->add('url', UrlType::class, [
                'label' => 'URL',
                'attr' => ['class' => 'form-control']
            ])
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'titre',
                'label' => 'Playlist',
                'attr' => ['class' => 'form-control'],
                'choices' => $playlists
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
} 