using System.ComponentModel.DataAnnotations;
using System.Collections.Generic;

namespace WebHackingPortal.Models
{
    public class Lab
    {
        [Key]
        public int Id { get; set; }

        [Required]
        public string Title { get; set; } = string.Empty;

        [Required]
        public string Description { get; set; } = string.Empty;

        public string? Category { get; set; }

        public ICollection<Challenge> Challenges { get; set; } = new List<Challenge>();
    }
}