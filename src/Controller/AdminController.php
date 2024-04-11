<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use App\Entity\Clients;
use App\Entity\Commande;
use App\Entity\Materiel;
use App\Form\ClientType;
use App\Form\CommandeType;
use App\Form\MaterielType;
use App\Entity\CommandeMateriel;
use App\Repository\UserRepository;
use App\Repository\ClientsRepository;
use App\Repository\CommandeRepository;
use App\Repository\MaterielRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    
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

   

    #[Route('/deleteM/{id}', name: 'app_delete_materiel')]
    public function deleteM($id, MaterielRepository $rep, PersistenceManagerRegistry $doctrine ): Response
    {

        //recuperer la classe a supprimer
        $materiels = $rep->find($id);
        $rep=$doctrine->getManager();
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
        $commandes = $client->getCommandes();

        // Render the client information and their commands in a template
        return $this->render('admin/detailClient.html.twig', [
            'client' => $client,
            'commandes' => $commandes,
        ]);
    }

    #[Route('/commandes/{id}', name: 'app_commandes')]
    public function showCommandes($id, PersistenceManagerRegistry $doctrine): Response
    {
        // Retrieve the client information from the database based on the provided id
        $client = $doctrine->getRepository(Clients::class)->find($id);

        // Check if the client exists
        if (!$client) {
            throw $this->createNotFoundException('Client not found');
        }

        // Retrieve the commands for this client
        $commandes = $client->getCommandes();

        // Render the client's commands in a template
        return $this->render('admin/commandes/commandesByClient.html.twig', [
            'commandes' => $commandes,
            'client' => $client,
        ]);
    }



    #[Route('/generatePdf/{clientId}/{commandId}', name: 'generate_pdf')]
    public function generatePdf(Pdf $snappy, int $clientId, int $commandId, PersistenceManagerRegistry $doctrine)
    {
        // Fetch the client details from the database using the ID
        $entityManager = $doctrine->getManager();
        $clientRepository = $entityManager->getRepository(Clients::class);
        $client = $clientRepository->find($clientId);

        if (!$client) {
            throw $this->createNotFoundException('Client not found with ID: ' . $clientId);
        }

        // Fetch the specific command associated with the client
        $commandeRepository = $entityManager->getRepository(Commande::class);
        $commande = $commandeRepository->findOneBy(['id' => $commandId, 'client' => $client]);

        if (!$commande) {
            throw $this->createNotFoundException('Command not found with ID: ' . $commandId);
        }

        // Render the Twig template and pass the client details and the command
        $html = $this->renderView('/resources/devis.html.twig', [
            'client' => $client,
            'commande' => $commande,
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

    
    //generate addCommande function
    #[Route('/liste_commande', name: 'app_liste_commandes')]
    public function addCommande(PersistenceManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $commandes = $em->getRepository(Commande::class)->findAll();

        return $this->render('admin/commandes/listeCommandes.html.twig', [
            'commandes' => $commandes,
            
        ]);
    }

    //generate ajoutCommande function
    #[Route('/ajout_commande', name: 'app_ajout_commande')]
public function ajoutCommande(Request $request, PersistenceManagerRegistry $doctrine): Response
{

    $clients = $doctrine->getRepository(Clients::class)->findAll();
        $materiels = $doctrine->getRepository(Materiel::class)->findAll();

    if ($request->isMethod('POST')) {
        // Get the submitted data
        $formData = $request->request->all();

        // Create a new Commande entity and set its properties
        $commande = new Commande();
        $commande->setCode($formData['code']);
        $commande->setQte($formData['qte']);
        $commande->setPuht($formData['puht']);
        $commande->setTtva($formData['ttva']);
        $commande->setRemise($formData['remise']);
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
        $materielId = $formData['materiel'];
        $materiel = $doctrine->getRepository(Materiel::class)->find($materielId);

        // Create a new CommandeMateriel entity and set its properties
        $commandeMateriel = new CommandeMateriel();
        $commandeMateriel->setCommande($commande);
        $commandeMateriel->setMateriel($materiel);
        // Set other properties of CommandeMateriel as needed

        // Persist the CommandeMateriel entity
        $entityManager->persist($commandeMateriel);

        $entityManager->flush();
        $this->addFlash('success', 'Commande added successfully');
        return $this->redirectToRoute('app_liste_commandes');
    }
    return $this->render('admin/commandes/ajoutCommande.html.twig', [
        'clients' => $clients,
        'materiels' => $materiels,
    
    ]);
}




    //generate deleteCommande function
    #[Route('/delete_commande/{id}', name: 'app_delete_commande')]
    public function deleteCommande($id, CommandeRepository $rep, PersistenceManagerRegistry $doctrine ): Response
    {

        //recuperer la classe a supprimer
        $commandes = $rep->find($id);
        $rep=$doctrine->getManager();
        //supprimer la classe        
        $rep->remove($commandes);
        $rep->flush();
        //flash message
        $this->addFlash('success', 'Commande removed!');
        return $this->redirectToRoute('app_liste_commandes'); 
        
    }   

    //generate updateCommande function
    #[Route('/update_commande/{id}', name: 'app_update_commande')]
    public function updateCommande($id, Request $request, CommandeRepository $rep, PersistenceManagerRegistry $doctrine): Response
    {
        // récupérer la classe à modifier
        $commandes = $rep->find($id);
        // créer un formulaire
        $form = $this->createForm(CommandeType::class, $commandes);
        // récupérer les données saisies
        $form->handleRequest($request);
        // vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // récupérer les données saisies
            $commandes = $form->getData();
            // persister les données
            $rep = $doctrine->getManager();
            $rep->persist($commandes);
            $rep->flush();
            //flash message
            $this->addFlash('success', 'La commande a été mis à jour avec succès!');
            return $this->redirectToRoute('app_liste_commandes');
        }
        return $this->render('admin/commandes/editCommande.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    



}
