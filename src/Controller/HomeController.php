<?php

namespace App\Controller;

use App\Repository\SitesRepository;
use App\Repository\SortiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SortiesRepository $sortie, SitesRepository $sites, Request $req): Response
    {
        $lstSortie = $sortie->filtersHomeSorties($req);
        $lstSites = $sites->findAll();

        $memory = [];
        if($req->get('filter_site') != null){
            $memory['site'] = $req->get('filter_site');
        }else if($req->get('filter_site') == null){
            $memory['site'] = 'default';
        }

        if($req->get('filter_d1') != null and $req->get('filter_d2') != null){
            $memory['d1'] = $req->get('filter_d1');
            $memory['d2'] = $req->get('filter_d2');
        }
        
      //  dd($memory);
        return $this->render('home/index.html.twig', [
            'lstSortie' => $lstSortie,
            'lstSites' => $lstSites,
            'memory' => $memory,
        ]);
    }
}
