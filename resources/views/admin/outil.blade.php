@extends('layouts.app')
@section('icon', 'fa-solid fa-screwdriver-wrench')
@section('h1', 'Outils')

@section('content')

    <style>
        .criteria {
            margin-top: 20px;
        }

        .criterion {
            margin-bottom: 5px;
            color: red;
            /* Couleur par défaut */
        }

        .criterion.valid {
            color: green;
            /* Couleur lorsque valide */
        }
    </style>

    <div class="row">
        <div class="col-6">

            <div class="card">

                <div class="card-header">
                    <h5><i class="fa-solid fa-key"></i> Vérificateur de complexité de mot de passe</h5>
                </div>

                <!-- /.card-header -->
                <div class="card-body">

                    <form class="row g-3">
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Prénom :</label>
                            <input type="text" class="form-control" id="firstName" placeholder="Votre prénom">
                        </div>
                        <div class="col-md-6">
                            <label for="lastName" class="form-label">Nom :</label>
                            <input type="text" class="form-control" id="lastName" placeholder="Votre nom">
                        </div>
                        <div class="col-md-6">
                            <label for="lastName" class="form-label">UID :</label>
                            <input type="text" class="form-control" id="UID" placeholder="Votre UID">
                        </div>
                        <div class="col-md-6">
                            <label for="lastName" class="form-label">Appuyer pour générer l'UID</label>
                            <button type="button" class="form-control btn btn-primary" onclick="generateUID()">Générer
                                UID</button>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe :</label>
                            <input type="password" class="form-control" id="password">
                        </div>

                        <div class="criteria text-center">
                            <div class="criterion" id="length">Longueur minimale de 12 caractères</div>
                            <div class="criterion" id="specialChars">Caractères spéciaux</div>
                            <div class="criterion" id="lowerCase">Lettres minuscules</div>
                            <div class="criterion" id="upperCase">Lettres majuscules</div>
                            <div class="criterion" id="numbers">Chiffres</div>
                            <div class="criterion" id="userRelated">Ne contient pas le nom, prénom ou UID</div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


@endsection

@section('scriptjs')
    <script>
        function generateUID() {
            var firstName = document.getElementById('firstName').value.trim();
            var lastName = document.getElementById('lastName').value.trim();

            if (firstName && lastName) {
                var uid = firstName.charAt(0).toLowerCase() + lastName.toLowerCase().replace(/ /g, '');
                document.getElementById('UID').value = uid;
                // Utiliser uid comme nécessaire (vous pouvez le stocker ou l'afficher, par exemple)
            } else {
                console.log("Veuillez entrer le prénom et le nom pour générer l'UID.");
            }
        }

        function checkPassword() {
            var password = document.getElementById('password').value;

            // Obtention du nom, prénom et UID
            var firstName = document.getElementById('firstName').value.trim();
            var lastName = document.getElementById('lastName').value.trim();
            var uid = (firstName && lastName) ? firstName.charAt(0).toLowerCase() + lastName.toLowerCase().replace(/ /g,
                '') : "";

            // Vérification des critères du mot de passe
            var lengthCriterion = document.getElementById('length');
            var specialCharsCriterion = document.getElementById('specialChars');
            var lowerCaseCriterion = document.getElementById('lowerCase');
            var upperCaseCriterion = document.getElementById('upperCase');
            var numbersCriterion = document.getElementById('numbers');
            var userRelatedCriterion = document.getElementById('userRelated');

            // Vérification des critères
            if (password.length >= 12) {
                lengthCriterion.classList.add('valid');
            } else {
                lengthCriterion.classList.remove('valid');
            }

            var regexSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
            if (regexSpecial.test(password)) {
                specialCharsCriterion.classList.add('valid');
            } else {
                specialCharsCriterion.classList.remove('valid');
            }

            var regexLowerCase = /[a-z]+/;
            if (regexLowerCase.test(password)) {
                lowerCaseCriterion.classList.add('valid');
            } else {
                lowerCaseCriterion.classList.remove('valid');
            }

            var regexUpperCase = /[A-Z]+/;
            if (regexUpperCase.test(password)) {
                upperCaseCriterion.classList.add('valid');
            } else {
                upperCaseCriterion.classList.remove('valid');
            }

            var regexNumbers = /[0-9]+/;
            if (regexNumbers.test(password)) {
                numbersCriterion.classList.add('valid');
            } else {
                numbersCriterion.classList.remove('valid');
            }

            // Vérification si le mot de passe contient des informations utilisateur
            if (
                password.toLowerCase().includes(firstName.toLowerCase()) ||
                password.toLowerCase().includes(lastName.toLowerCase()) ||
                password.includes(uid) ||
                checkSubstring(password, firstName) ||
                checkSubstring(password, lastName)
            ) {
                userRelatedCriterion.classList.remove('valid');
            } else {
                userRelatedCriterion.classList.add('valid');
            }
        }

        function checkSubstring(password, name) {
            for (var i = 0; i < name.length - 3; i++) {
                var sub = name.substring(i, i + 4).toLowerCase();
                if (password.toLowerCase().includes(sub)) {
                    return true;
                }
            }
            return false;
        }

        // Appeler checkPassword à chaque changement dans le champ de mot de passe
        document.getElementById('password').addEventListener('input', checkPassword);
    </script>
@endsection
