<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use App\Entity\Clients;
use App\Entity\Facture;
use App\Entity\Devi;
use App\Entity\Materiel;
use App\Form\ClientType;
use App\Form\MaterielType;
use App\Entity\FactureMateriel;
use App\Entity\DeviMateriel;
use App\Repository\UserRepository;
use App\Repository\ClientsRepository;
use App\Repository\FactureRepository;
use App\Repository\DeviRepository;
use App\Repository\MaterielRepository;
use App\Repository\FactureMaterielRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\DeviMaterielRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(PersistenceManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $clientRepository = $em->getRepository(Clients::class);
        $totalClients = $clientRepository->createQueryBuilder('c')
            ->select('count(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'total_clients' => $totalClients,
        ]);
    }

// .........................................Gestion des clients................................................
    #[Route('/liste_des_clients', name: 'app_liste_clients')]
    public function ListeClients(PersistenceManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $users = $em->getRepository(Clients::class)->findAll();

        $clients = new Clients();
        $form = $this->createForm(ClientType::class, $clients);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($clients);
            $entityManager->flush();
            $this->addFlash('success', 'Client ajouté avec succès');
            return $this->redirectToRoute('app_liste_clients');
        }


        return $this->render('admin/clients/ListeClients.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users,  
            'addClient' =>$form->createView(),
        ]);
    }

    #[Route('/detail_client/{id}', name: 'app_more')]
    public function show($id, PersistenceManagerRegistry $doctrine): Response
    {
        // Retrieve the client information from the database based on the provided id
        $client = $doctrine->getRepository(Clients::class)->find($id);

        // Check if the client exists
        if (!$client) {
            throw $this->createNotFoundException('Client not found');
        }

        // Retrieve the commands for this client
        $commandes = $client->getDevis();

        // Render the client information and their commands in a template
        return $this->render('admin/clients/detailClient.html.twig', [
            'client' => $client,
            'commandes' => $commandes,
        ]);
    }

    #[Route('/update_client/{id}', name: 'app_edit_client')]
    public function updateClient($id, Request $request, ClientsRepository $rep, PersistenceManagerRegistry $doctrine): Response
    {
        // récupérer la classe à modifier
        $clients = $rep->find($id);
        // créer un formulaire
        $form = $this->createForm(ClientType::class, $clients);
        // récupérer les données saisies
        $form->handleRequest($request);
        // vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // récupérer les données saisies
            $clients = $form->getData();
            // persister les données
            $rep = $doctrine->getManager();
            $rep->persist($clients);
            $rep->flush();
            //flash message
            $this->addFlash('success', 'Le client a été mis à jour avec succès!');
            return $this->redirectToRoute('app_liste_clients');
        }
        return $this->render('admin/clients/updateClient.html.twig', [
            'editForm' => $form->createView(),
            'client' => $clients,
            
        ]);
    }

    #[Route('/delete_client/{id}', name: 'app_delete_client')]
    public function deleteClient($id, ClientsRepository $rep, PersistenceManagerRegistry $doctrine ): Response
    {

        //recuperer la classe a supprimer
        $clients = $rep->find($id);
        $rep=$doctrine->getManager();
        //supprimer la classe        
        $rep->remove($clients);
        $rep->flush();
        //flash message
        $this->addFlash('success', 'Client removed!');
        return $this->redirectToRoute('app_liste_clients'); 
        
    }

    #[Route('/devisByClient/{id}', name: 'app_devis')]
    public function showDevisByClient($id, PersistenceManagerRegistry $doctrine): Response
    {
        // Retrieve the client information from the database based on the provided id
        $client = $doctrine->getRepository(Clients::class)->find($id);

        // Check if the client exists
        if (!$client) {
            throw $this->createNotFoundException('Client not found');
        }

        // Retrieve the commands for this client
        $commandes = $client->getDevis();

        // Fetch the CommandeMateriel entities associated with each Commande
        $commandeMaterielRepository = $doctrine->getRepository(DeviMateriel::class);
        $commandeMateriels = [];
        foreach ($commandes as $commande) {
            $commandeMateriels[$commande->getId()] = $commandeMaterielRepository->findBy(['devi' => $commande]);
        }

        // Render the client's commands in a template
        return $this->render('admin/clients/devisByClient.html.twig', [
            'commandes' => $commandes,
            'client' => $client,
            'commandeMateriels' => $commandeMateriels,
        ]);
    }

    #[Route('/factureByClient/{id}', name: 'app_facture')]
    public function showFactureByClient($id, PersistenceManagerRegistry $doctrine): Response
    {
        // Retrieve the client information from the database based on the provided id
        $client = $doctrine->getRepository(Clients::class)->find($id);

        // Check if the client exists
        if (!$client) {
            throw $this->createNotFoundException('Client not found');
        }

        // Retrieve the commands for this client
        $commandes = $client->getFactures();

        // Fetch the CommandeMateriel entities associated with each Commande
        $commandeMaterielRepository = $doctrine->getRepository(FactureMateriel::class);
        $commandeMateriels = [];
        foreach ($commandes as $commande) {
            $commandeMateriels[$commande->getId()] = $commandeMaterielRepository->findBy(['facture' => $commande]);
        }

        // Render the client's commands in a template
        return $this->render('admin/clients/factureByClient.html.twig', [
            'commandes' => $commandes,
            'client' => $client,
            'commandeMateriels' => $commandeMateriels,
        ]);
    }

// .........................................Gestion des materiels................................................


    #[Route('/deleteM/{id}', name: 'app_delete_materiel')]
    public function deleteM($id, MaterielRepository $rep, PersistenceManagerRegistry $doctrine ): Response
    {
        //recuperer la classe a supprimer
        $materiels = $rep->find($id);
        $rep=$doctrine->getManager();

        // Get the CommandeMateriel entities that reference the Materiel
        $commandeMateriels = $materiels->getDeviMateriels();

        // Remove the CommandeMateriel entities and their associated Commande
        foreach ($commandeMateriels as $commandeMateriel) {
            $commande = $commandeMateriel->getDevi();
            $rep->remove($commandeMateriel);
            $rep->remove($commande);
        }

        //supprimer la classe        
        $rep->remove($materiels);
        $rep->flush();
        //flash message
        $this->addFlash('success', 'Materiel supprimé!');
        return $this->redirectToRoute('app_stock'); 
    }


    #[Route('/liste_des_materiels', name: 'app_stock')]
    public function stock(PersistenceManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $materiels = $em->getRepository(Materiel::class)->findAll();

        $stock = new Materiel();
        $form = $this->createForm(MaterielType::class, $stock);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($stock);
            $entityManager->flush();
            $this->addFlash('success', 'Materiel ajouté avec succès');
            return $this->redirectToRoute('app_stock');
        }


        return $this->render('admin/materiels/ListeMateriels.html.twig', [
            'controller_name' => 'AdminController',
            'materiels' => $materiels,  
            'addMateriel' =>$form->createView(),
        ]);
    }

    #[Route('/update_materiel/{id}', name: 'app_update_materiel')]
    public function updateM($id, Request $request, MaterielRepository $rep, PersistenceManagerRegistry $doctrine): Response
    {
        // récupérer la classe à modifier
        $materiels = $rep->find($id);
        // créer un formulaire
        $form = $this->createForm(MaterielType::class, $materiels);
        // récupérer les données saisies
        $form->handleRequest($request);
        // vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // récupérer les données saisies
            $materiels = $form->getData();
            // persister les données
            $rep = $doctrine->getManager();
            $rep->persist($materiels);
            $rep->flush();
            //flash message
            $this->addFlash('success', 'Le matériel a été mis à jour avec succès!');
            return $this->redirectToRoute('app_stock');
        }
        return $this->render('admin/materiels/EditMateriel.html.twig', [
            'form' => $form->createView(),
        ]);
    }


// .........................................Gestion des devis...............................................
    

  

    #[Route('/liste_devis', name: 'app_liste_devis')]
    public function listDevis(PersistenceManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $commandeRepository = $em->getRepository(Devi::class);

        $code = $request->query->get('code');
        $date = $request->query->get('date');

        // Create the query builder
        $queryBuilder = $commandeRepository->createQueryBuilder('c');

        // Apply filters
        
        if ($code) {
            $queryBuilder->orWhere('c.code LIKE :code')
                ->setParameter('code', '%' . $code . '%');
        }

        if ($date) {
            $queryBuilder->andWhere('c.date = :date') // Adjust this based on your actual date field name
                ->setParameter('date', new \DateTime($date));
        }

        // Get the query
        $query = $queryBuilder->getQuery();

        // Get the result of the query
        $commandes = $query->getResult();

        return $this->render('admin/devis/listeDevis.html.twig', [ 
        'commandes' => $commandes,
        ]);
    }

    #[Route('/detail_devis/{id}', name: 'app_detail_devis')]
    public function detailDevis($id, DeviRepository $rep, DeviMaterielRepository $repcm): Response
    {
        $commande = $rep->find($id);
        $client = $commande->getClient(); // Get the client associated with the commande
        
        $commandeMaterielRepository = $repcm->findAll();
        return $this->render('admin/devis/detailDevis.html.twig', [
            'commande' => $commande,
            'client' => $client,
            'commandeMateriels' => $commandeMaterielRepository,
            
        ]);
    }

    #[Route('/ajout_devis', name: 'app_ajout_devis')]
    public function ajoutDevis(Request $request, PersistenceManagerRegistry $doctrine): Response
    {

        $clients = $doctrine->getRepository(Clients::class)->findAll();
        $materiels = $doctrine->getRepository(Materiel::class)->findAll();

        if ($request->isMethod('POST')) {
            // Get the submitted data
            $formData = $request->request->all();

            // Check if a Commande with the same code already exists
        $existingCommande = $doctrine->getRepository(Devi::class)->findOneBy(['code' => $formData['code']]);
        if ($existingCommande) {
            $this->addFlash('error', 'Il y a déjà une commande avec ce code');
            return $this->render('admin/devis/ajoutDevis.html.twig', [
                'clients' => $clients,
                'materiels' => $materiels,
            ]);
        }

            // Create a new Commande entity and set its properties
            $commande = new Devi();
            $commande->setCode($formData['code']);
            $commande->setTimbre($formData['timbre']);
            $commande->setDate(new \DateTime($formData['date']));
            

            // Get the Client entity from the form data
            $clientId = $formData['client'];
            $client = $doctrine->getRepository(Clients::class)->find($clientId);
            $commande->setClient($client);

            // Persist the Commande entity
            $entityManager = $doctrine->getManager();
            $entityManager->persist($commande);

            // Get the Materiel entity from the form data
            $materielIds = $formData['materiel'];
            $quantities = $formData['quantity'];
            $prices = $formData['price'];
            // $tva = $formData['tva'];
            // $remise = $formData['remise'];

            foreach ($materielIds as $index => $materielId) {
                $materiel = $doctrine->getRepository(Materiel::class)->find($materielId);
    
                // Create a new CommandeMateriel entity and set its properties
                $commandeMateriel = new DeviMateriel();
                $commandeMateriel->setDevi($commande);
                $commandeMateriel->setMateriel($materiel);
                $commandeMateriel->setQte($quantities[$index]);
                $commandeMateriel->setPrix($prices[$index]);
                $commandeMateriel->setTva(19);
                $commandeMateriel->setRemise(0);
    
                // Persist the CommandeMateriel entity
                $entityManager->persist($commandeMateriel);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Commande added successfully');
            return $this->redirectToRoute('app_liste_devis');
        }
        return $this->render('admin/devis/ajoutDevis.html.twig', [
            'clients' => $clients,
            'materiels' => $materiels,
        
        ]);
    }

    #[Route('/delete_devis/{id}', name: 'app_delete_devis')]
    public function deleteDevis($id, DeviRepository $rep, PersistenceManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $commande = $rep->find($id);

        // Check if the commande exists
        if (!$commande) {
            throw $this->createNotFoundException('No commande found for id '.$id);
        }

        // Remove associated CommandeMateriel entities
        foreach ($commande->getDeviMateriels() as $commandeMateriel) {
            $entityManager->remove($commandeMateriel);
        }

        // Now remove the Commande entity
        $entityManager->remove($commande);
        $entityManager->flush();

        // Flash message
        $this->addFlash('success', 'Commande removed!');
        return $this->redirectToRoute('app_liste_commandes');
    }


    #[Route('/edit_devis/{id}', name: 'app_update_devis')]
    public function editDevi(Request $request, PersistenceManagerRegistry $doctrine, Devi $commande): Response
    {
        $clients = $doctrine->getRepository(Clients::class)->findAll();
        $materiels = $doctrine->getRepository(Materiel::class)->findAll();

        if ($request->isMethod('POST')) {
            // Get the submitted data
            $formData = $request->request->all();

            // Set the Commande entity's properties
            $commande->setCode($formData['code']);
            $commande->setTimbre($formData['timbre']);
            $commande->setDate(new \DateTime($formData['date']));

            // Get the Client entity from the form data
            $clientId = $formData['client'];
            $client = $doctrine->getRepository(Clients::class)->find($clientId);
            $commande->setClient($client);

            // Get the Materiel entity from the form data
            $materielIds = $formData['materiel'];
            $quantities = $formData['quantity'];
            $prices = $formData['price'];

            // Get the existing CommandeMateriel entities
            $existingCommandeMateriels = $doctrine->getRepository(DeviMateriel::class)->findBy(['devi' => $commande]);

            foreach ($existingCommandeMateriels as $existingCommandeMateriel) {
                // Check if the existing CommandeMateriel entity is in the form data
                if (!in_array($existingCommandeMateriel->getMateriel()->getId(), $materielIds)) {
                    // If it's not in the form data, remove it
                    $doctrine->getManager()->remove($existingCommandeMateriel);
                }
            }

            

            foreach ($materielIds as $index => $materielId) {
                $materiel = $doctrine->getRepository(Materiel::class)->find($materielId);

                // Check if a CommandeMateriel entity already exists
                $commandeMateriel = $doctrine->getRepository(DeviMateriel::class)->findOneBy([
                    'devi' => $commande,
                    'materiel' => $materiel,
                ]);

                // If it doesn't exist, create a new one
                if (!$commandeMateriel) {
                    $commandeMateriel = new DeviMateriel();
                    $commandeMateriel->setDevi($commande);
                    $commandeMateriel->setMateriel($materiel);
                }

                // Set or update the properties
                $commandeMateriel->setQte($quantities[$index]);
                $commandeMateriel->setPrix($prices[$index]);
                $commandeMateriel->setTva(19);
                $commandeMateriel->setRemise(0);

                // Persist the CommandeMateriel entity
                $doctrine->getManager()->persist($commandeMateriel);
            }

            $removedMaterielIdsString = $request->request->get('removedMaterielIds');
            $removedMaterielIds = explode(',', $removedMaterielIdsString);

            foreach ($removedMaterielIds as $removedMaterielId) {
                // Find the CommandeMateriel entity
                $commandeMateriel = $doctrine->getRepository(DeviMateriel::class)->findOneBy([
                    'devi' => $commande,
                    'materiel' => $removedMaterielId,
                ]);

                // If the CommandeMateriel entity exists, remove it
                if ($commandeMateriel) {
                    $doctrine->getManager()->remove($commandeMateriel);
                }
            }

            // Flush the changes to the database
            $doctrine->getManager()->flush();

            $this->addFlash('success', 'Commande mise à jour avec succès');

            return $this->redirectToRoute('app_liste_devis');
        }

        return $this->render('admin/devis/editDevis.html.twig', [
            'commande' => $commande,
            'clients' => $clients,
            'materiels' => $materiels,
        ]);
    }


    #[Route('/generatePdf/{clientId}/{commandId}', name: 'generate_pdf_devis')]
    public function generatePdf(Pdf $snappy, int $clientId, int $commandId, PersistenceManagerRegistry $doctrine)
    {
        // Fetch the client details from the database using the ID
        $entityManager = $doctrine->getManager();
        $clientRepository = $entityManager->getRepository(Clients::class);
        $client = $clientRepository->find($clientId);

        if (!$client) {
            throw $this->createNotFoundException('Client not found with ID: ' . $clientId);
        }

        $commandes = $entityManager->getRepository(Devi::class)->findAll();

        // Fetch the specific command associated with the client
        $commandeRepository = $entityManager->getRepository(Devi::class);
        $commande = $commandeRepository->findOneBy(['id' => $commandId, 'client' => $client]);

        if (!$commande) {
            throw $this->createNotFoundException('Command not found with ID: ' . $commandId);
        }

        // Fetch the CommandeMateriel entities associated with the Commande
        $commandeMaterielRepository = $entityManager->getRepository(DeviMateriel::class);
        $commandeMateriels = $commandeMaterielRepository->findBy(['devi' => $commande]);

        // Render the Twig template and pass the client details, the command, and the CommandeMateriels
        $html = $this->renderView('/resources/devis.html.twig', [
            'client' => $client,
            'commande' => $commande,
            'commandes' => $commandes,
            'commandeMateriels' => $commandeMateriels,
        ]);

        $pdfContent = $snappy->getOutputFromHtml($html);

        // You can return the PDF as a response
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="devis.pdf"',
            ]
        );
    }


// .........................................Gestion des factures...............................................


    #[Route('/liste_factures', name: 'app_liste_factures')]
    public function listFactures(PersistenceManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $commandeRepository = $em->getRepository(Facture::class);

        $code = $request->query->get('code');
        $date = $request->query->get('date');

        // Create the query builder
        $queryBuilder = $commandeRepository->createQueryBuilder('c');

        // Apply filters
        
        if ($code) {
            $queryBuilder->orWhere('c.code LIKE :code')
                ->setParameter('code', '%' . $code . '%');
        }

        if ($date) {
            $queryBuilder->andWhere('c.date = :date') // Adjust this based on your actual date field name
                ->setParameter('date', new \DateTime($date));
        }

        // Get the query
        $query = $queryBuilder->getQuery();

        // Get the result of the query
        $commandes = $query->getResult();

        return $this->render('admin/factures/listeFactures.html.twig', [
            'commandes' => $commandes,
        ]);
    }





    #[Route('/detail_facture/{id}', name: 'app_detail_facture')]
    public function detailFacture($id, FactureRepository $rep, FactureMaterielRepository $repcm): Response
    {
        $commande = $rep->find($id);
        $client = $commande->getClient(); // Get the client associated with the commande
        
        $commandeMaterielRepository = $repcm->findAll();
        return $this->render('admin/factures/detailFacture.html.twig', [
            'commande' => $commande,
            'client' => $client,
            'commandeMateriels' => $commandeMaterielRepository,
            
        ]);
    }

    
    #[Route('/ajout_facture', name: 'app_ajout_facture')]
    public function ajoutFacture(Request $request, PersistenceManagerRegistry $doctrine): Response
    {

        $clients = $doctrine->getRepository(Clients::class)->findAll();
        $materiels = $doctrine->getRepository(Materiel::class)->findAll();

        if ($request->isMethod('POST')) {
            // Get the submitted data
            $formData = $request->request->all();

            // Check if a Commande with the same code already exists
        $existingCommande = $doctrine->getRepository(Facture::class)->findOneBy(['code' => $formData['code']]);
        if ($existingCommande) {
            $this->addFlash('error', 'Il y a déjà une commande avec ce code');
            return $this->render('admin/factures/ajoutFacture.html.twig', [
                'clients' => $clients,
                'materiels' => $materiels,
            ]);
        }

            // Create a new Commande entity and set its properties
            $commande = new Facture();
            $commande->setCode($formData['code']);
            $commande->setTimbre($formData['timbre']);
            $commande->setDate(new \DateTime($formData['date']));
            

            // Get the Client entity from the form data
            $clientId = $formData['client'];
            $client = $doctrine->getRepository(Clients::class)->find($clientId);
            $commande->setClient($client);

            // Persist the Commande entity
            $entityManager = $doctrine->getManager();
            $entityManager->persist($commande);

            // Get the Materiel entity from the form data
            $materielIds = $formData['materiel'];
            $quantities = $formData['quantity'];
            $prices = $formData['price'];
            // $tva = $formData['tva'];
            // $remise = $formData['remise'];

            foreach ($materielIds as $index => $materielId) {
                $materiel = $doctrine->getRepository(Materiel::class)->find($materielId);
    
                // Create a new CommandeMateriel entity and set its properties
                $commandeMateriel = new FactureMateriel();
                $commandeMateriel->setFacture($commande);
                $commandeMateriel->setMateriel($materiel);
                $commandeMateriel->setQte($quantities[$index]);
                $commandeMateriel->setPrix($prices[$index]);
                $commandeMateriel->setTva(19);
                $commandeMateriel->setRemise(0);
    
                // Persist the CommandeMateriel entity
                $entityManager->persist($commandeMateriel);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Commande added successfully');
            return $this->redirectToRoute('app_liste_factures');
        }
        return $this->render('admin/factures/ajoutFacture.html.twig', [
            'clients' => $clients,
            'materiels' => $materiels,
        
        ]);
    }

    #[Route('/delete_facture/{id}', name: 'app_delete_facture')]
    public function deleteFacture($id, FactureRepository $rep, PersistenceManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $commande = $rep->find($id);

        // Check if the commande exists
        if (!$commande) {
            throw $this->createNotFoundException('No commande found for id '.$id);
        }

        // Remove associated CommandeMateriel entities
        foreach ($commande->getDeviMateriels() as $commandeMateriel) {
            $entityManager->remove($commandeMateriel);
        }

        // Now remove the Commande entity
        $entityManager->remove($commande);
        $entityManager->flush();

        // Flash message
        $this->addFlash('success', 'Facture supprimée!');
        return $this->redirectToRoute('app_liste_factures');
    }


    #[Route('/edit_facture/{id}', name: 'app_update_facture')]
    public function editFacture(Request $request, PersistenceManagerRegistry $doctrine, Facture $commande): Response
    {
        $clients = $doctrine->getRepository(Clients::class)->findAll();
        $materiels = $doctrine->getRepository(Materiel::class)->findAll();

        if ($request->isMethod('POST')) {
            // Get the submitted data
            $formData = $request->request->all();

            // Set the Commande entity's properties
            $commande->setCode($formData['code']);
            $commande->setTimbre($formData['timbre']);
            $commande->setDate(new \DateTime($formData['date']));

            // Get the Client entity from the form data
            $clientId = $formData['client'];
            $client = $doctrine->getRepository(Clients::class)->find($clientId);
            $commande->setClient($client);

            // Get the Materiel entity from the form data
            $materielIds = $formData['materiel'];
            $quantities = $formData['quantity'];
            $prices = $formData['price'];

            // Get the existing CommandeMateriel entities
            $existingCommandeMateriels = $doctrine->getRepository(FactureMateriel::class)->findBy(['facture' => $commande]);

            foreach ($existingCommandeMateriels as $existingCommandeMateriel) {
                // Check if the existing CommandeMateriel entity is in the form data
                if (!in_array($existingCommandeMateriel->getMateriel()->getId(), $materielIds)) {
                    // If it's not in the form data, remove it
                    $doctrine->getManager()->remove($existingCommandeMateriel);
                }
            }

            

            foreach ($materielIds as $index => $materielId) {
                $materiel = $doctrine->getRepository(Materiel::class)->find($materielId);

                // Check if a CommandeMateriel entity already exists
                $commandeMateriel = $doctrine->getRepository(FactureMateriel::class)->findOneBy([
                    'facture' => $commande,
                    'materiel' => $materiel,
                ]);

                // If it doesn't exist, create a new one
                if (!$commandeMateriel) {
                    $commandeMateriel = new FactureMateriel();
                    $commandeMateriel->setFacture($commande);
                    $commandeMateriel->setMateriel($materiel);
                }

                // Set or update the properties
                $commandeMateriel->setQte($quantities[$index]);
                $commandeMateriel->setPrix($prices[$index]);
                $commandeMateriel->setTva(19);
                $commandeMateriel->setRemise(0);

                // Persist the CommandeMateriel entity
                $doctrine->getManager()->persist($commandeMateriel);
            }

            $removedMaterielIdsString = $request->request->get('removedMaterielIds');
            $removedMaterielIds = explode(',', $removedMaterielIdsString);

            foreach ($removedMaterielIds as $removedMaterielId) {
                // Find the CommandeMateriel entity
                $commandeMateriel = $doctrine->getRepository(FactureMateriel::class)->findOneBy([
                    'facture' => $commande,
                    'materiel' => $removedMaterielId,
                ]);

                // If the CommandeMateriel entity exists, remove it
                if ($commandeMateriel) {
                    $doctrine->getManager()->remove($commandeMateriel);
                }
            }

            // Flush the changes to the database
            $doctrine->getManager()->flush();

            $this->addFlash('success', 'Commande mise à jour avec succès');

            return $this->redirectToRoute('app_liste_factures');
        }

        return $this->render('admin/factures/editFacture.html.twig', [
            'commande' => $commande,
            'clients' => $clients,
            'materiels' => $materiels,
        ]);
    }



    #[Route('/generatePdffacture/{clientId}/{commandId}', name: 'generate_pdf_facture')]
    public function generatePdfFacture(Pdf $snappy, int $clientId, int $commandId, PersistenceManagerRegistry $doctrine)
    {
        // Fetch the client details from the database using the ID
        $entityManager = $doctrine->getManager();
        $clientRepository = $entityManager->getRepository(Clients::class);
        $client = $clientRepository->find($clientId);

        if (!$client) {
            throw $this->createNotFoundException('Client not found with ID: ' . $clientId);
        }

        $commandes = $entityManager->getRepository(Facture::class)->findAll();

        // Fetch the specific command associated with the client
        $commandeRepository = $entityManager->getRepository(Facture::class);
        $commande = $commandeRepository->findOneBy(['id' => $commandId, 'client' => $client]);

        if (!$commande) {
            throw $this->createNotFoundException('Command not found with ID: ' . $commandId);
        }

        // Fetch the CommandeMateriel entities associated with the Commande
        $commandeMaterielRepository = $entityManager->getRepository(FactureMateriel::class);
        $commandeMateriels = $commandeMaterielRepository->findBy(['facture' => $commande]);

        // Render the Twig template and pass the client details, the command, and the CommandeMateriels
        $html = $this->renderView('/resources/facture.html.twig', [
            'client' => $client,
            'commande' => $commande,
            'commandes' => $commandes,
            'commandeMateriels' => $commandeMateriels,
        ]);

        $pdfContent = $snappy->getOutputFromHtml($html);

        // You can return the PDF as a response
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="facture.pdf"',
            ]
        );
    }





}
