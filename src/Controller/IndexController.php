<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\PropertySearch;
use App\Entity\CategorySearch;
use App\Entity\PriceSearch;
use App\Form\ArticleType;
use App\Form\CategoryType;
use App\Form\PropertySearchType;
use App\Form\CategorySearchType;
use App\Form\PriceSearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    // LISTE DES ARTICLES AVEC RECHERCHE PAR NOM
    #[Route('/home', name: 'article_list')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $propertySearch = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $propertySearch);
        $form->handleRequest($request);

        $articles = $entityManager->getRepository(Article::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $propertySearch->getNom();
            
            if ($nom != "") {
                $articles = $entityManager->getRepository(Article::class)->findBy([
                    'nom' => $nom
                ]);
            } else {
                $articles = $entityManager->getRepository(Article::class)->findAll();
            }
        }

        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
            'form' => $form->createView()
        ]);
    }

    // AJOUTER UN ARTICLE (AVEC ArticleType)
    #[Route('/article/new', name: 'new_article', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();
            
            $this->addFlash('success', 'Article créé avec succès!');
            return $this->redirectToRoute('article_list');
        }
        
        return $this->render('articles/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // AJOUTER UNE CATÉGORIE
    #[Route('/category/new', name: 'new_category', methods: ['GET', 'POST'])]
    public function newCategory(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            
            $this->addFlash('success', 'Catégorie créée avec succès!');
            return $this->redirectToRoute('article_list');
        }
        
        return $this->render('categories/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // AFFICHER LES DÉTAILS
    #[Route('/article/{id}', name: 'article_show')]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);
        
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }
        
        return $this->render('articles/show.html.twig', [
            'article' => $article,
        ]);
    }

    // MODIFIER UN ARTICLE (AVEC ArticleType)
    #[Route('/article/edit/{id}', name: 'edit_article', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);
        
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }
        
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Article modifié avec succès!');
            return $this->redirectToRoute('article_list');
        }
        
        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    // SUPPRIMER UN ARTICLE
    #[Route('/article/delete/{id}', name: 'delete_article', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);
        
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }
        
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
            
            $this->addFlash('success', 'Article supprimé avec succès!');
        }
        
        return $this->redirectToRoute('article_list');
    }

    // RECHERCHE DES ARTICLES PAR CATÉGORIE
    #[Route('/art_cat', name: 'article_par_cat')]
    public function articlesParCategorie(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorySearch = new CategorySearch();
        $form = $this->createForm(CategorySearchType::class, $categorySearch);
        $form->handleRequest($request);

        $articles = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $categorySearch->getCategory();

            if ($category) {
                $articles = $category->getArticles();
            } else {
                $articles = $entityManager->getRepository(Article::class)->findAll();
            }
        }

        return $this->render('articles/articlesParCategorie.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
        ]);
    }

    // RECHERCHE DES ARTICLES PAR PRIX
    #[Route('/art_prix', name: 'article_par_prix')]
    public function articlesParPrix(Request $request, EntityManagerInterface $entityManager): Response
    {
        $priceSearch = new PriceSearch();
        $form = $this->createForm(PriceSearchType::class, $priceSearch);
        $form->handleRequest($request);

        $articles = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $minPrice = $priceSearch->getMinPrice();
            $maxPrice = $priceSearch->getMaxPrice();

            if ($minPrice !== null && $maxPrice !== null) {
                $articles = $entityManager->getRepository(Article::class)
                    ->findByPriceRange($minPrice, $maxPrice);
            }
        }

        return $this->render('articles/articlesParPrix.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
        ]);
    }
}