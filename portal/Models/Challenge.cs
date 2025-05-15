using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;

namespace WebHackingPortal.Models
{
    public class Challenge
    {
        [Key]
        public int Id { get; set; }

        public string Title { get; set; } = string.Empty;
        public string Description { get; set; } = string.Empty;
        public string Flag { get; set; } = string.Empty;

        public int Points { get; set; }
        public int Order { get; set; }

        // Foreign key to Lab
        public int LabId { get; set; }
        public Lab? Lab { get; set; }

        public ICollection<Submission> Submissions { get; set; } = new List<Submission>();
    }
}