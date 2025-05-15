using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using WebHackingPortal.Data;
using WebHackingPortal.Models;
using System.Threading.Tasks;
using System.Linq;
using Microsoft.EntityFrameworkCore; // For ToListAsync(), FirstOrDefaultAsync()
using Microsoft.AspNetCore.Http;
using Microsoft.Extensions.Logging;      // For Session extensions
namespace WebHackingPortal.Pages
{
    public class LoginModel : PageModel
    {
        private readonly AppDbContext _db;
        private readonly ILogger<LoginModel> _logger;
        public bool ShowUsernameForm { get; set; } = false;
        public AppUser? CurrentUser { get; set; }

        public LoginModel(AppDbContext db, ILogger<LoginModel> logger)
        {
            _db = db;
            _logger = logger;
        }


        public async Task<IActionResult> OnGetAsync(string action)
        {
            // Force show the form if ?action=change is present
            if (action == "change")
            {

                ShowUsernameForm = true;
                return Page();
            }

            var users = await _db.Users.ToListAsync();

            if (users.Count == 0)
            {
                ShowUsernameForm = true;
            }
            else
            {
                CurrentUser = users.First();
                HttpContext.Session.SetString("userId", CurrentUser.Id.ToString());
                await HttpContext.Session.CommitAsync(); // Save session
                _logger.LogInformation("User {UserId} logged in.", CurrentUser.Id);
                return RedirectToPage("/Index");
            }

            _logger.LogInformation("No users found, showing username form.");
            return Page();
        }

        public async Task<IActionResult> OnPostAsync(string username, string action)
        {
            if (action == "change")
            {
                return RedirectToPage("/Login");
            }

            if (!string.IsNullOrWhiteSpace(username))
            {
                var user = await _db.Users
                    .FirstOrDefaultAsync(u => u.Username == username);

                if (user == null)
                {
                    // No user with this name exists → create new one
                    user = new AppUser { Username = username };
                    _db.Users.Add(user);
                }
                else
                {
                    // Optional: Update username of existing user
                    // This ensures even if user exists, their name reflects latest choice
                    user.Username = username;
                    _db.Users.Update(user);
                }
                _logger.LogInformation("Saving user {UserId} with username {Username}.", user.Id, username);    
                await _db.SaveChangesAsync();
                _logger.LogInformation("User {UserId} logged in.", user.Id);
                HttpContext.Session.SetString("userId", user.Id.ToString());
                await HttpContext.Session.CommitAsync();
            }

            return RedirectToPage("/Index");
        }
    }
}