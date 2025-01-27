<?php

namespace App\Controller;

use App\Repository\BordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

class DinamicMenyController extends AbstractController
{

    public function bord(int $id, BordRepository $repository): Response
    {
        $book = $repository->find($id);

        $menu = [];
        foreach ($book->getMatiere() as $matiere){
            $matieres[] = ['name'=> $matiere->getTitle(), 'id'=> $matiere->getId()];
        }
        foreach ($book->getClasse() as $classe){
            $classes[] = ['name'=> $classe->getTitle(), 'id'=> $classe->getId()];
        }
        foreach ($book->getFiliere() as $filiere){
            $filieres[] = ['name'=> $filiere->getTitle(), 'id'=> $filiere->getId()];
        }
        $elements = [
            'matiere' => $matieres,
            'classe' => $classes,
            'filiere' => $filieres
        ];
        $combinations = $this->generateCombinations($elements, [], 30);
        foreach ($combinations as $combination) {
            $nameParts = [];
            $slugParts = [];

            if (isset($combination['matiere'])) {
                $nameParts[] = $combination['matiere']['name'];
                $slugParts[] = 'm' . $combination['matiere']['id'];
            }

            if (isset($combination['classe'])) {
                $nameParts[] = $combination['classe']['name'];
                $slugParts[] = 'c' . $combination['classe']['id'];
            }

            if (isset($combination['filiere'])) {
                $nameParts[] = $combination['filiere']['name'];
                $slugParts[] = 'f' . $combination['filiere']['id'];
            }
            $slugger = new AsciiSlugger('fr');
            $slug = $slugger->slug(strtolower(implode(' ', $nameParts) . '-' . implode('', $slugParts)));
            if(isset($nameParts[1])) $nameParts[1] = ' en ' . $nameParts[1];
            $name = implode(' ', $nameParts);

            $menu[] = [
                'name' => $name,
                'slug' => $slug,
            ];
        }
        /* fin de la creation du menu*/

        return $this->render('dinamic_meny/bord.html.twig', [
            'menu' => $menu,
        ]);
    }

    static function generateCombinations(array $elements, array $prefix = [], int $max = 30)
    {
        static $count = 0; // Compteur statique pour suivre le nombre de combinaisons générées
        $result = [];

        if ($count >= $max) {
            return $result;
        }

        if (empty($elements)) {
            if (!empty($prefix)) {
                $result[] = $prefix;
                $count++;
            }
            return $result;
        }

        $category = array_keys($elements)[0];
        $values = array_shift($elements);

        if (empty($values)) {
            $result = array_merge($result, self::generateCombinations($elements, $prefix, $max));
        } else {
            foreach ($values as $value) {
                if ($count >= $max) {
                    break;
                }
                $newPrefix = $prefix;
                $newPrefix[$category] = $value;
                $result = array_merge($result, self::generateCombinations($elements, $newPrefix, $max));
            }
            $result = array_merge($result, self::generateCombinations($elements, $prefix, $max));
        }

        return $result;
    }
}
