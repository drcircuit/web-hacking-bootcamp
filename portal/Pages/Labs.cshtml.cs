using Microsoft.AspNetCore.Mvc.RazorPages;
using WebHackingPortal.Services;
using WebHackingPortal.Models; // Ensure this matches the namespace where 'Lab' is defined
using System.Collections.Generic;

namespace WebHackingPortal.Pages
{
    public class LabsModel : PageModel
    {
        private readonly LabService _labService;

        public IEnumerable<Lab> Labs => _labService.GetAllLabs();

        public LabsModel(LabService labService)
        {
            _labService = labService;
        }

        public void OnGet()
        {
            // No extra logic needed — labs are already loaded by LabService
        }
    }
}