<?php

namespace App\View\Components\Navigation;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public $menuItems;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->menuItems = $this->getMenuItems();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navigation.sidebar');
    }

    /**
     * Get the menu items structure based on user role
     */
    private function getMenuItems()
    {
        $user = auth()->user();

        if (!$user) {
            return [];
        }

        // Admin Menu - Full Access (New Guard)
        if ($user->isAdmin()) {
            $menu = [
                [
                    'title' => __('app.tableau_de_bord'),
                    'icon' => 'fas fa-tachometer-alt',
                    'route' => 'admin.dashboard',
                    'active' => request()->routeIs(['admin.dashboard'])
                ],
                [
                    'title' => __('app.gestion_academique'),
                    'icon' => 'fas fa-university',
                    'children' => [
                        [
                            'title' => __('app.etudiants'),
                            'route' => 'admin.etudiants.index',
                            'active' => request()->routeIs('admin.etudiants.*')
                        ],
                        [
                            'title' => __('app.enseignants'),
                            'route' => 'admin.enseignants.index',
                            'active' => request()->routeIs('admin.enseignants.*')
                        ],
                        [
                            'title' => __('app.classes'),
                            'route' => 'admin.classes.index',
                            'active' => request()->routeIs('admin.classes.*')
                        ],
                        [
                            'title' => __('app.cours'),
                            'route' => 'admin.cours.index',
                            'active' => request()->routeIs('admin.cours.*')
                        ],
                        [
                            'title' => __('app.evaluations'),
                            'route' => 'admin.evaluations.index',
                            'active' => request()->routeIs('admin.evaluations.*')
                        ],
                    ]
                ],
                [
                    'title' => __('app.resultats_scolaires'),
                    'icon' => 'fas fa-chart-line',
                    'children' => [
                        [
                            'title' => __('app.notes'),
                            'route' => 'admin.notes.index',
                            'active' => request()->routeIs('admin.notes.index')
                        ],
                        [
                            'title' => __('app.releves_notes'),
                            'route' => 'admin.rapports.notes.transcript-index',
                            'active' => request()->routeIs('admin.rapports.notes.*')
                        ],
                    ]
                ],
            ];

                // Settings / Parameters (show sensitive ones only to super_admins)
                $parametresMenu = (function () use ($user) {
                    $children = [];

                    // 2FA setup should be visible to any admin so users (admins) can enroll
                    $children[] = [
                        'title' => __('app.two_factor'),
                        'route' => 'admin.2fa.setup',
                        'active' => request()->routeIs('admin.2fa.*')
                    ];

                    // IP Security is sensitive and should be managed only by super_admin
                    if ($user->hasRole('super_admin')) {
                        $children[] = [
                            'title' => __('app.securite_ip'),
                            'route' => 'admin.settings.ip',
                            'active' => request()->routeIs('admin.settings.ip*')
                        ];
                    }

                    if (empty($children)) {
                        return null;
                    }

                    return [
                        'title' => __('app.parametres'),
                        'icon' => 'fas fa-cogs',
                        'children' => $children,
                    ];
                })();

                if ($parametresMenu) {
                    $menu[] = $parametresMenu;
                }

            // Only append the admin-management tools for super_admins
            if ($user->hasRole('super_admin')) {
                $menu[] = [
                    'title' => __('app.gestion_admins'),
                    'icon' => 'fas fa-users-cog',
                    'children' => [
                        [
                            'title' => __('app.liste_admins'),
                            'route' => 'admin.admins.index',
                            'active' => request()->routeIs('admin.admins.*')
                        ],
                        [
                            'title' => __('app.creer_admin'),
                            'route' => 'admin.admins.create',
                            'active' => request()->routeIs('admin.admins.create')
                        ],
                        [
                            'title' => __('app.activity_logs'),
                            'route' => 'admin.logs.index',
                            'active' => request()->routeIs('admin.logs.*')
                        ],
                    ]
                ];
            }

            return $menu;
        }

        // Admin Menu - Full Access (Old Role)
        if ($user && $user->hasRole('admin')) {
            return [
                [
                    'title' => __('app.gestion_academique'),
                    'icon' => 'fas fa-graduation-cap',
                    'children' => [
                        [
                            'title' => __('app.classes'),
                            'route' => 'classes.index',
                            'active' => request()->routeIs('classes.*')
                        ],
                        [
                            'title' => __('app.etudiants'),
                            'route' => 'etudiants.index',
                            'active' => request()->routeIs('etudiants.*')
                        ],
                        [
                            'title' => __('app.enseignants'),
                            'route' => 'enseignants.index',
                            'active' => request()->routeIs('enseignants.*')
                        ],
                        [
                            'title' => __('app.courses'),
                            'route' => 'cours.index',
                            'active' => request()->routeIs('cours.*')
                        ],
                        [
                            'title' => __('app.evaluations'),
                            'route' => 'evaluations.index',
                            'active' => request()->routeIs('evaluations.*')
                        ],
                        [
                            'title' => __('app.notes'),
                            'route' => 'notes.index',
                            'active' => request()->routeIs('notes.*')
                        ]
                    ]
                ],
                [
                    'title' => __('app.gestion_financiere'),
                    'icon' => 'fas fa-money-bill-wave',
                    'children' => [
                        [
                            'title' => __('app.paiements_etudiants'),
                            'route' => 'paiements.etudiants.index',
                            'active' => request()->routeIs('paiements.etudiants.*')
                        ],
                        [
                            'title' => __('app.paiements_enseignants'),
                            'route' => 'paiements.enseignants.index',
                            'active' => request()->routeIs('paiements.enseignants.*')
                        ]
                    ]
                ],
                [
                    'title' => __('app.rapports'),
                    'icon' => 'fas fa-chart-bar',
                    'children' => [
                        [
                            'title' => __('app.releve_de_notes'),
                            'route' => 'rapports.notes.transcript-index',
                            'active' => request()->routeIs('rapports.notes.transcript*')
                        ]
                    ]
                ]
            ];
        }

        // Teacher Menu - Limited Access
        if ($user->hasRole('teacher')) {
            return [
                [
                    'title' => __('app.tableau_de_bord'),
                    'icon' => 'fas fa-tachometer-alt',
                    'route' => 'enseignant.dashboard',
                    'active' => request()->routeIs(['enseignant.dashboard'])
                ],
                [
                    'title' => __('app.mon_enseignement'),
                    'icon' => 'fas fa-chalkboard-teacher',
                    'children' => [
                        [
                            'title' => __('app.mes_etudiants'),
                            'route' => 'enseignant.mes-etudiants',
                            'active' => request()->routeIs('enseignant.mes-etudiants')
                        ],
                        [
                            'title' => __('app.mes_cours'),
                            'route' => 'enseignant.mes-cours',
                            'active' => request()->routeIs('enseignant.mes-cours')
                        ],
                        [
                            'title' => __('app.saisir_notes'),
                            'route' => 'enseignant.saisir-notes',
                            'active' => request()->routeIs('enseignant.saisir-notes')
                        ]
                    ]
                ],
                [
                    'title' => __('app.mon_profil'),
                    'icon' => 'fas fa-user-circle',
                    'route' => 'enseignant.profil',
                    'active' => request()->routeIs('enseignant.profil')
                ]
            ];
        }

        // Student Menu - Very Limited Access
        if ($user->hasRole('etudiant')) {
            return [
                [
                    'title' => __('app.tableau_de_bord'),
                    'icon' => 'fas fa-tachometer-alt',
                    'route' => 'etudiant.dashboard',
                    'active' => request()->routeIs(['etudiant.dashboard'])
                ],
                [
                    'title' => __('app.my_journey'),
                    'icon' => 'fas fa-user-graduate',
                    'children' => [
                        [
                            'title' => __('app.my_grades'),
                            'route' => 'etudiant.mes-notes',
                            'active' => request()->routeIs('etudiant.mes-notes')
                        ],
                        [
                            'title' => __('app.mon_emploi_du_temps'),
                            'route' => 'etudiant.mon-emploi',
                            'active' => request()->routeIs('etudiant.mon-emploi')
                        ]
                    ]
                ],
                [
                    'title' => __('app.public_search'),
                    'icon' => 'fas fa-search',
                    'route' => 'rechercher-notes',
                    'active' => request()->routeIs('rechercher-notes')
                ]
            ];
        }

        return [];
    }
}
