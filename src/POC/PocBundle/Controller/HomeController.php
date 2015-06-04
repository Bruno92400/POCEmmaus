<?php

namespace POC\PocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    private function initArticles(){
        $listArticle = array(
              array('id' => 1, 'categorie' => 'voiture', 'title' => 'Peugot 206', 'description' => 'Tres tres bien', 'prix' => 20, 'cheminImage' => 'images/voiture.jpg'),
              array('id' => 2, 'categorie' => 'informatique', 'title' => 'Un super PC', 'description' => 'Tres tres bien', 'prix' => 30, 'cheminImage' => 'images/pc.jpg'),
              array('id' => 3, 'categorie' => 'informatique', 'title' => 'Tablette qui envoi du lourd', 'description' => 'Tres tres bien', 'prix' => 40, 'cheminImage' => 'images/tablette.jpg'),
              array('id' => 4, 'categorie' => 'informatique', 'title' => 'Un PC portable comme on en a jamais vu', 'description' => 'Tres tres bien', 'prix' => 50, 'cheminImage' => 'images/pc_portable.jpg'),
              array('id' => 5, 'categorie' => 'ameublement', 'title' => 'Table basse super bien', 'prix' => 60, 'description' => 'Tres tres bien', 'cheminImage' => 'images/meuble.jpg'),
              array('id' => 6, 'categorie' => 'ameublement', 'title' => 'Canapé super comfort', 'prix' => 70, 'description' => 'Tres tres bien', 'cheminImage' => 'images/canape.jpg')
        );

        return $listArticle;
    }

    public function connexionAction(Request $request){
        if($request->getSession()->get('user') !== null){
            $request->getSession()->set('user', '');
        }

        $form = $this->createFormBuilder()
            ->add('nom', 'text', array(
                    'required'  => true,
                    'attr' => array(
                         'class' => 'form-control'
                     )
            ))
            ->add('connexion', 'submit', array(
                    'attr' => array(
                         'class' => 'btn btn-primary'
                     )
            ))
        ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            if(!$request->getSession()->isStarted()){
                $session = new Session();
                $session->start();

                $listArticle = $this->initArticles();

                $session->set('listArticle', $listArticle);
            } else {
                $session = $request->getSession();
            }

            $data = $form->getData();

            $session->set('user', $data['nom']);

            return $this->redirect($this->generateUrl('poc_poc_homepage'));
        }

        return $this->render('POCPocBundle:Home:connexion.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function indexAction(Request $request)
    {
        $session = $request->getSession();

        return $this->render('POCPocBundle:Home:index.html.twig', array(
          'listArticle' => $session->get('listArticle')
        ));
    }

    public function searchResultAction(Request $request, $searchTitle, $categorie)
    {
        $session = $request->getSession();
        $listArticle = $session->get('listArticle');

        $returnListArticle = array();

        foreach ($listArticle as $article) {
            $save = 0;

            if($searchTitle !== 'none'){
                if(preg_match('/' . $searchTitle . '/', strtolower($article['title']))){
                    $save = 1;
                }
            } else {
                $save = 1;
            }

            if($categorie !== 'categorie' && $save === 1){
                if ($categorie === $article['categorie']){
                    $save = 1;
                } else {
                    $save = 0;
                }
            }

            if ($save === 1){
                $returnListArticle[] = $article;
            }
        }

        return $this->render('POCPocBundle:Home:searchResult.html.twig', array(
          'listArticle' => $returnListArticle
        ));
    }

    public function viewAction(Request $request, $id){
        $session = $request->getSession();
        $listArticle = $session->get('listArticle');

        foreach ($listArticle as $article) {
            if($article['id'] == $id){
                return $this->render('POCPocBundle:Home:view.html.twig', array(
                  'article' => $article
                ));
            }
        }
    }

    public function addAction(Request $request){
        $form = $this->createFormBuilder()
            ->add('categorie', 'choice', array(
                    'choices'   => array('voiture' => 'Voiture', 'informatique' => 'Informatique', 'ameublement' => 'Ameublement'),
                    'required'  => true,
                     'attr' => array(
                         'class' => 'form-control'
                     )
            ))
            ->add('title', 'text', array(
                    'required'  => true,
                    'attr' => array(
                         'class' => 'form-control'
                     )
            ))
            ->add('description', 'textarea', array(
                    'required'  => true,
                    'attr' => array(
                         'class' => 'form-control'
                     )
            ))
            ->add('prix', 'text', array(
                    'required'  => true,
                    'attr' => array(
                         'class' => 'form-control'
                     )
            ))
            ->add('save', 'submit', array(
                    'label' => 'Enregistrer',
                    'attr' => array(
                         'class' => 'btn btn-primary'
                     )
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $session = $request->getSession();
            $listArticle = $session->get('listArticle');

            $articleAdding = array('id' => count($listArticle) + 1, 'categorie' => $data['categorie'], 'title' => $data['title'], 'description' => $data['description'], 'prix' => $data['prix'], 'cheminImage' => '');
            $listArticle[] = $articleAdding;

            $session->set('listArticle', $listArticle);

            return $this->redirect($this->generateUrl('poc_poc_homepage'));
        }

        return $this->render('POCPocBundle:Home:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function deleteAction(Request $request, $id){
        $session = $request->getSession();
        $listArticle = $session->get('listArticle');

        foreach ($listArticle as $key => $article) {
            if($article['id'] == $id){
                unset($listArticle[$key]);
            }
        }

        $session->set('listArticle', $listArticle);

        return $this->render('POCPocBundle:Home:index.html.twig', array(
          'listArticle' => $session->get('listArticle')
        ));
    }
}
