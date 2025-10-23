<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *     title="API Gestion de Compte",
 *     version="1.0",
 *     description="API pour la gestion des comptes bancaires"
 * )
 *
 * @OA\Tag(name="Auth", description="Authentification")
 * @OA\Tag(name="Comptes", description="Gestion des comptes bancaires")
 * @OA\Tag(name="Clients", description="Gestion des clients")
 * @OA\Tag(name="Admins", description="Gestion des administrateurs")
 * @OA\Tag(name="Transactions", description="Gestion des transactions")
 *
 * @OA\Schema(
 *     schema="Compte",
 *     @OA\Property(property="id", type="string", description="ID du compte"),
 *     @OA\Property(property="numeroCompte", type="string", description="Numéro du compte"),
 *     @OA\Property(property="titulaire", type="string", description="Nom du titulaire"),
 *     @OA\Property(property="type", type="string", enum={"cheque", "epargne"}, description="Type de compte"),
 *     @OA\Property(property="solde", type="number", format="float", description="Solde calculé"),
 *     @OA\Property(property="devise", type="string", description="Devise"),
 *     @OA\Property(property="dateCreation", type="string", format="date", description="Date de création"),
 *     @OA\Property(property="statut", type="string", enum={"actif", "bloque", "ferme"}, description="Statut"),
 *     @OA\Property(property="metadata", ref="#/components/schemas/Metadata")
 * )
 *
 * @OA\Schema(
 *     schema="Metadata",
 *     @OA\Property(property="derniereModification", type="string", format="date-time"),
 *     @OA\Property(property="version", type="integer")
 * )
 *
 * @OA\Schema(
 *     schema="Pagination",
 *     @OA\Property(property="currentPage", type="integer"),
 *     @OA\Property(property="totalPages", type="integer"),
 *     @OA\Property(property="totalItems", type="integer"),
 *     @OA\Property(property="itemsPerPage", type="integer"),
 *     @OA\Property(property="hasNext", type="boolean"),
 *     @OA\Property(property="hasPrevious", type="boolean")
 * )
 *
 * @OA\Schema(
 *     schema="Links",
 *     @OA\Property(property="self", type="string"),
 *     @OA\Property(property="first", type="string"),
 *     @OA\Property(property="last", type="string"),
 *     @OA\Property(property="next", type="string", nullable=true),
 *     @OA\Property(property="previous", type="string", nullable=true)
 * )
 */
class Definitions
{
}
