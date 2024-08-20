This project was developed using the provided Symfony template.

## Customizations

1. **Tailwind CSS**:
   ```bash
   php bin/console tailwind:build -w

## Environment Setup

2. **Clone the Repository**:
   ```bash
   git clone https://github.com/isaiasxavier/dutch-soccer-league.git

## Brief Explanation

This Symfony application fetches and stores data from an external API into a relational database.

3. **Migration**:
   ```bash
   php bin/console doctrine:migrations:migrate

4. **FetchDataCommand: Fetches data from the API and populates the database.**:
   ```bash
   php bin/console FetchDataCommand

# Service Documentation

### ApiService

- **getTeams(): array**  
  Retrieves a list of teams from the `DED` competition.

- **getMatchesDed(): array**  
  Retrieves a list of matches from the `DED` competition.

- **getStanding(): array**  
  Retrieves the standings for the `DED` competition.

### PaginationService

- **getPaginationParameters(Request $request): array**  
  Extracts pagination parameters (`limit` and `offset`) from the request query, with default values of 10 and 0
  respectively.

# Routes Documentation

## HomepageController

- **`/`**: Displays the homepage with current season, competition, standings, and team standings.

## RegistrationController

- **`/register`**: Displays the registration form, processes user registration, hashes the password, and logs in the
  user.

## LoginController

- **`/login`**: Shows the login form and handles authentication errors.
- **`/logout`**: Logs out the user (intercepted by Symfony's security).
- **`/redirect-logout`**: Redirects to the logout route.

## DashboardController

- **`/dashboard`**: Displays the dashboard with paginated teams and user-specific data.

## FollowController

- **`/follow/{id}`**: Allows the user to follow a team. Displays follow errors and followed teams.
- **`/followed-teams`**: Lists all teams followed by the user.
- **`/unfollow/{id}`**: Unfollows a team and redirects to the list of followed teams.

## Entities Overview

### **Coach**

- **Attributes**:
    - `id`
    - `firstName`
    - `lastName`
    - `date`
    - `nationality`
    - `contractStart`
    - `contractUntil`
- **Relationships**:
    - `team` (One-to-One with `Team`)

### **Competition**

- **Attributes**:
    - `id`
    - `name`
    - `code`
    - `type`
    - `emblem`

### **GameMatch**

- **Attributes**:
    - `id`
    - `status`
    - `matchday`
    - `stage`
    - `lastUpdated`
    - `homeTeamId`
    - `awayTeamId`
    - `homeTeamScoreFullTime`
    - `awayTeamScoreFullTime`
    - `homeTeamScoreHalfTime`
    - `awayTeamScoreHalfTime`
    - `scoreWinner`
    - `scoreDuration`
    - `refereeId`
    - `refereeName`
    - `dateGame`
- **Relationships**:
    - `homeTeam` (Many-to-One with `Team`)
    - `awayTeam` (Many-to-One with `Team`)

### **Player**

- **Attributes**:
    - `id`
    - `name`
    - `position`
    - `date`
    - `nationality`
- **Relationships**:
    - `team` (Many-to-One with `Team`)

### **Season**

- **Attributes**:
    - `id`
    - `startDate`
    - `endDate`
    - `currentMatchday`
    - `winner`
- **Relationships**:
    - `competition` (Many-to-One with `Competition`)

### **Standing**

- **Attributes**:
    - `stage`
    - `type`
    - `groupName`
- **Relationships**:
    - `season` (Many-to-One with `Season`)

### **SeasonTeamStanding**

- **Attributes**:
    - `position`
    - `playedGames`
    - `form`
    - `won`
    - `draw`
    - `lost`
    - `points`
    - `goalsFor`
    - `goalsAgainst`
    - `goalDifference`
- **Relationships**:
    - `standing` (Many-to-One with `Standing`)
    - `team` (Many-to-One with `Team`)

### **User**

- Attributes:
    - `id`
    - `email`
    - `roles`
    - `password`

### **Follow**

- Attributes:
    - `id`
- Relationships:
    - `user` (Many-to-One with `User`)
    - `team` (Many-to-One with `Team`)

# Repository Documentation

### CoachRepository

- **findCoachByTeamId($teamId): ?Coach**  
  Retrieves a `Coach` entity associated with a specified team ID.

### CompetitionRepository

- **findByName(string $name): ?Competition**  
  Retrieves a `Competition` entity by its name.

### FollowRepository

- **getFollowedTeamIds($user): array**  
  Retrieves the IDs of all teams followed by a given user.

- **getFollowedTeamsByUser($user): array**  
  Retrieves all teams followed by a given user.

- **followTeamAction($user, $team): array**  
  Allows a user to follow a team. Validates and persists the follow action.

- **unfollowTeam($user, $teamId): void**  
  Allows a user to unfollow a team based on the team ID.

### GameMatchRepository

- **countMatchesByTeamId($teamId): int**  
  Counts the number of matches involving the specified team, either as home or away.

- **findMatchesByTeamId($teamId, $limit, $offset): array**  
  Retrieves a paginated list of matches involving the specified team, either as home or away.

### PlayerRepository

- **findPlayerByTeamId($teamId): array**  
  Retrieves all players associated with the specified team.

### SeasonRepository

- **findCurrentSeasonByCompetition(Competition $competition): ?Season**  
  Retrieves the current season associated with the given competition.

### SeasonTeamStandingRepository

- **findByStandings(array $standings): array**  
  Retrieves `SeasonTeamStanding` entities associated with the given list of standings.

### TeamRepository

- **findTeamById($id): ?Team**  
  Retrieves a `Team` entity by its ID.

- **findTeamsByIds(array $ids): array**  
  Retrieves `Team` entities by their IDs.

- **findTeamsWithPaginationAndCountAndFollowers(Request $request, $user): array**  
  Retrieves `Team` entities with pagination, total count, and a list of followed team IDs by the given user.

### UserRepository

- **upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void**  
  Upgrades (rehashes) the password of a user.

- **findUserByEmail(string $email): ?User**  
  Retrieves a `User` entity by email address.

### RegistrationFormType

- **buildForm(FormBuilderInterface $builder, array $options): void**  
  Configures the form fields for user registration, including email, terms agreement, and password with validation
  constraints.

- **configureOptions(OptionsResolver $resolver): void**  
  Sets default options for the form, specifying that the data class is `User`.

### UserFixtures

- **load(ObjectManager $manager): void**  
  Populates the database with initial user data for testing or development, including creating and persisting user
  entities.

### LoginAuthenticator

- **authenticate(Request $request): Passport**  
  Retrieves email and password from the request, creates a Passport object for user authentication, including CSRF
  protection.

- **onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response**  
  Redirects to the originally requested page or to the dashboard upon successful authentication.

- **getLoginUrl(Request $request): string**  
  Returns the URL to the login page.

### FollowValidator

- **validate($value, Constraint $constraint): void**  
  Validates a `Follow` entity to ensure that both the `user` and `team` properties are not null, adding violations if
  any are missing.

# Template Documentation

### dashboard.html.twig

- **Purpose:** Template file for rendering the user dashboard view.
- **Features:** Displays user-specific data such as recent activities, notifications, and other relevant information.

### follow.html.twig

- **Purpose:** Template file for rendering the view of followed teams for the user.
- **Features:** Displays a list of teams that the user is following, including team name, emblem, and links for
  viewing details or unfollowing.

### homepage.html.twig

- **Purpose:** Template file for rendering the homepage view, specifically displaying the standings of teams in a
  football competition.
- **Features:** Shows the competition name, season years, and competition emblem prominently at the top of the page.
  Displays team standings, including their position, name, emblem, and points in a table format.

### register.html.twig

- **Purpose:** Template file for rendering the user registration form.
- **Features:**
    - Displays a user-friendly registration form with fields for email, password, and agreement to terms.
    - Utilizes Symfony's form_row to render form fields with Tailwind CSS classes for consistent styling.
    - Includes error handling to display validation errors prominently above the form fields.

### login.html.twig

- **Purpose:** Template file for rendering the user login form.
- **Features:**
- Displays a login form with fields for email and password.
- Shows error messages if the login attempt fails.
- Includes a condition to inform the user if they are already logged in.

### detail.html.twig

- **Purpose:** This template displays detailed information about a football team, including the team's name, logo,
  founding date, address, website, club colors, and venue. It also provides details on the coach, squad, and recent or
  upcoming matches.
- **Features:**
- Organized Layout: Divided into sections for team details, coach and squad information, and match listings.
- Paginated Navigation: Includes "Previous" and "Next" buttons for paginated match viewing.
- Availability Check: Shows a message if team information is not available.
- Interactive Elements: Styled links for the team's website and pagination controls enhance user interaction.