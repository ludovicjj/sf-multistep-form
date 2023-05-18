# Multi step form avec symfony 6

Utilisation détourner du composant workflow pour afficher 3 formulaires à la suite, donnant l'impression d'un formulaire scindé en 3 étapes et de la session pour conserver les données des étapes précédentes.
Voir ``config/packages/workflow.yaml`` pour plus de détail sur les "places/étapes". Envoi d'un email de confirmation lors de la transition ``to_confirmed`` grace aux évènements (voir ``Listener/BookingFormSubscriber.php``). 

Si le workflow est de type ``state_machine`` il définit la valeur de ``property`` comme une chaine de caractère.

Si le workflow est de type ``workflow`` il définit la valeur de ``property`` comme un array.

## Stack:
* php 8.1
* symfony 6
* workflow
* docker
* SQLite

## Docker image:
* schickling/mailcatcher

## Workflow type:

``type: 'workflow' # or 'state_machine'``

Si le workflow est de type ``worflow`` et le nom du workflow est ``blog_publishing`` il peut être récupéré dans une class de la façon suivante :

````php
public function __construct(blogPublishingWorkflow) {
}
````

Si le workflow est de type ``state_machine`` et le nom du workflow est ``blog_publishing`` il peut être récupéré dans une class de la façon suivante :

````php
public function __construct(blogPublishingStateMachine) {
}
````

## workflow.yaml:

```yaml
framework:
    workflows:
        booking:
            type: 'state_machine'
            marking_store:
                type: 'method'
                property: 'currentPlace'
            supports:
                - App\Entity\BookingContainer
            places:
                - step1
                - step2
                - step3
                - confirmed
            transitions:
                to_step2:
                    from: step1
                    to: step2
                to_step3:
                    from: step2
                    to: step3
                to_confirmed:
                    from: step3
                    to: confirmed
```