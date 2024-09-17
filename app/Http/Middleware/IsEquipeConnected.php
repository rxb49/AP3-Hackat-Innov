<?php

namespace App\Http\Middleware;

use App\Utils\SessionHelpers;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsEquipeConnected
{
    /**
     * Un middleware est un mécanisme de filtrage des requêtes HTTP.
     * Dans notre cas, nous allons vérifier si l'équipe est connectée.
     *
     * Si l'équipe n'est pas connectée, on la redirige vers la page de connexion.
     * Sinon, on laisse passer la requête.
     *
     * Il est possible de modifier le comportement du middleware en fonction de nos besoins (exemple : vérifier si l'équipe est connectée et si elle a les droits nécessaires pour accéder à une ressource).
     *
     * Cette classe est utilisé dans le routeur pour vérifier si l'équipe est connectée. (voir routes/web.php)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'équipe n'est pas connectée, on la redirige vers la page de connexion
        if (!SessionHelpers::isConnected()) {
            return redirect("/login");
        }

        return $next($request);
    }
}
